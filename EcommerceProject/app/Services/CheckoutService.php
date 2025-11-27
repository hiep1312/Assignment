<?php

namespace App\Services;

use App\Enums\PaymentMethod;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductVariantInventoryRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CheckoutService
{
    public function __construct(
        protected OrderRepositoryInterface $repository,
        protected OrderItemRepositoryInterface $orderItemRepository,
        protected ProductVariantRepositoryInterface $variantRepository,
        protected ProductVariantInventoryRepositoryInterface $variantInventoryRepository,
        protected CartRepositoryInterface $cartRepository,
        protected CartItemRepositoryInterface $cartItemRepository,
        protected StripeService $stripeService
    ){}

    public function create(array $data): array
    {
        try {
            DB::beginTransaction();
            $sourceItems = collect();

            if(!empty($data['carts']) && is_array($data['carts'])) {
                $availableCartItems = $this->cartItemRepository->getAvailableByCartIds($data['carts'], true);
                $availableCartIds = $availableCartItems->pluck('cart_id')->unique();

                if($availableCartItems->isEmpty() || count($data['carts']) !== $availableCartIds->count()) {
                    throw new RuntimeException('Some carts are empty, expired, or contain items with insufficient stock.');
                }

                $sourceItems = $availableCartItems;
                $this->cartItemRepository->delete(
                    idOrCriteria: fn($query) => $query->whereIn('id', $availableCartIds->toArray())
                );
            }else {
                $variant = $this->variantRepository->first(
                    criteria: function($query) use ($data){
                        $query->with('inventory:variant_id,stock,sold_number');

                        $query->where('sku', $data['sku'])
                            ->where('status', 1)
                            ->whereHas('inventory', function($subQuery) use ($data){
                                $subQuery->where('stock', '>=', $data['quantity']);
                            })->sharedLock();
                    },
                    columns: ['id', 'price', 'discount'],
                );

                if(!$variant){
                    throw new RuntimeException('Product variant is unavailable or does not have sufficient stock.');
                }

                $sourceItems[] = (object) [
                    'product_variant_id' => $variant->id,
                    'quantity' => $data['quantity'],
                    'price' => $variant->discount ?? $variant->price,
                    'stock' => $variant->inventory->stock,
                    'sold_number' => $variant->inventory->sold_number
                ];
            }

            $totalAmount = 0;
            $shippingFee = 20_000;
            $orderItemsPayload = [];
            $stockAdjustments = [];

            $sourceItems->each(function($item) use (&$totalAmount, &$orderItemsPayload, &$stockAdjustments){
                $price = (int) $item->price;
                $quantity = (int) $item->quantity ?? 1;

                $totalAmount += $price * $quantity;
                $orderItemsPayload[] = [
                    'product_variant_id' => $item->product_variant_id,
                    'quantity' => $quantity,
                    'price' => $price
                ];
                $stockAdjustments[] = [
                    'variant_id' => $item->product_variant_id,
                    'stock' => $item->stock - $quantity,
                    'sold_number' => $item->sold_number + $quantity
                ];
            });

            $orderCreated = $this->repository->create([
                'user_id' => authPayload('sub'),
                'order_code' => (string) Str::uuid(),
                'total_amount' => $totalAmount,
                'shipping_fee' => $shippingFee,
                'status' => 1,
            ]);

            $orderItemsCreated = $orderCreated->items()->createMany($orderItemsPayload);
            $orderItemsCreated->load('productVariant.product');
            $orderCreated->setRelation('items', $orderItemsCreated);
            $this->variantInventoryRepository->upsert($stockAdjustments, ['variant_id']);
            DB::commit();

            return [
                'success' => true,
                'message' => 'Order created successfully.',
                'data' => $orderCreated
            ];

        }catch(QueryException $dbException) {
            DB::rollBack();
            Log::error('Database error during order creation', ['error' => $dbException]);

            return [
                'success' => false,
                'message' => 'An internal error occurred while creating the order.',
            ];

        }catch(Throwable $businessException) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $businessException->getMessage(),
            ];
        }
    }

    public function update(array $data, string $orderCode): array
    {
        try {
            DB::beginTransaction();
            $order = $this->repository->first(
                criteria: function($query) use ($orderCode){
                    $query->with('items.productVariant.inventory', 'items.productVariant.product')
                        ->where('order_code', $orderCode)
                        ->where('status', 1)
                        ->where('user_id', authPayload('sub'))
                        ->whereDoesntHave('payment')
                        ->sharedLock();
                }
            );

            if(!$order) {
                throw new RuntimeException('Order not found, already paid, or cannot be modified.');
            }

            $hasItemsChanged = false;
            if(!empty($data['items_update']) && is_array($data['items_update'])) {
                $orderItems = $order->items->keyBy('id');
                $orderItemsChanges = [];
                $stockAdjustments = [];
                $insufficientStockItems = [];

                foreach($data['items_update'] as ['item_id' => $itemId, 'quantity' => $quantity]) {
                    if($orderItems->has($itemId)) {
                        $orderItem = $orderItems->get($itemId);
                        $inventoryCurrent = $orderItem->productVariant->inventory;
                        $difference = $quantity - $orderItem->quantity;

                        if($difference === 0) continue;
                        elseif($difference > 0 && $inventoryCurrent->stock < $difference) {
                            $insufficientStockItems[] = $orderItem->productVariant->name ?? "Item #{$itemId}";
                            continue;
                        }

                        $orderItemsChanges[] = [
                            ...$orderItem->only('id', 'order_id', 'product_variant_id', 'price'),
                            'quantity' => $quantity,
                        ];

                        $stockAdjustments[] = [
                            'variant_id' => $orderItem->product_variant_id,
                            'stock' => $inventoryCurrent->stock - $difference,
                            'sold_number' => $inventoryCurrent->sold_number + $difference
                        ];

                        $orderItem->quantity = $quantity;
                    }
                }

                if(!empty($insufficientStockItems)) {
                    throw new RuntimeException("Insufficient stock for: " . implode(', ', $insufficientStockItems));
                }

                if(!empty($orderItemsChanges) && !empty($stockAdjustments)) {
                    $this->orderItemRepository->upsert($orderItemsChanges, ['id', 'order_id']);
                    $this->variantInventoryRepository->upsert($stockAdjustments, ['variant_id']);
                    $order->setRelation('items', $orderItems->values());
                    $hasItemsChanged = true;
                }
            }

            if(!empty($data['customer_note']) || $hasItemsChanged) {
                $attributesOrderToUpdate = [];

                if(!empty($data['customer_note'])){
                    $attributesOrderToUpdate['customer_note'] = $data['customer_note'];
                }

                if($hasItemsChanged){
                    $attributesOrderToUpdate['total_amount'] = DB::raw(<<<SQL
                        (SELECT SUM(oi.quantity * oi.price)
                        FROM order_items oi
                        WHERE oi.order_id = {$order->id})
                    SQL);
                }

                $this->repository->update(
                    idOrCriteria: $order->id,
                    attributes: $attributesOrderToUpdate,
                    rawEnabled: true
                );
            }

            DB::commit();
            foreach($order->items as $item) {
                $item->productVariant->unsetRelation('inventory');
            }

            return [
                'success' => true,
                'message' => 'Order updated successfully.',
                'data' => $order
            ];

        }catch(QueryException $dbException) {
            DB::rollBack();
            Log::error('Database error during order update', ['error' => $dbException, 'order_code' => $orderCode]);

            return [
                'success' => false,
                'message' => 'An internal error occurred while updating the order.',
            ];

        }catch(Throwable $businessException) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $businessException->getMessage(),
            ];
        }
    }

    public function cancel(string $orderCode): array
    {
        try {
            DB::beginTransaction();
            $order = $this->repository->first(
                criteria: function($query) use ($orderCode){
                    $query->with('items.productVariant.inventory', 'items.productVariant.product')
                        ->where('order_code', $orderCode)
                        ->where('status', 1)
                        ->where('user_id', authPayload('sub'))
                        ->whereDoesntHave('payment')
                        ->sharedLock();
                }
            );

            if(!$order) {
                throw new RuntimeException('Order not found, already paid, or cannot be cancelled.');
            }

            $cartItemsPayload = [];
            $stockAdjustments = [];

            foreach($order->items as $item){
                $cartItemsPayload[] = $item->only('product_variant_id', 'quantity', 'price');

                $stockAdjustments[] = [
                    'variant_id' => $item->product_variant_id,
                    'stock' => $item->productVariant->inventory->stock + $item->quantity,
                    'sold_number' => $item->productVariant->inventory->sold_number - $item->quantity
                ];
            }

            $restoredCart = $this->cartRepository->create([
                'user_id' => authPayload('sub'),
                'status' => 1,
                'expires_at' => now()->addDays(2)
            ]);

            $createdItems = $restoredCart->items()->createMany($cartItemsPayload);
            $restoredCart->setRelation('items', $createdItems);
            $this->variantInventoryRepository->upsert($stockAdjustments, ['variant_id']);
            $order->forceDelete();
            DB::commit();

            return [
                'success' => true,
                'message' => 'Order cancelled successfully. Items have been restored to your cart.',
                'data' => $restoredCart
            ];

        }catch(QueryException $dbException) {
            DB::rollBack();
            Log::error('Database error during order cancellation', ['error' => $dbException, 'order_code' => $orderCode]);

            return [
                'success' => false,
                'message' => 'An internal error occurred while cancelling the order.',
            ];

        }catch(Throwable $businessException) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $businessException->getMessage(),
            ];
        }
    }

    public function finalize(array $data, string $orderCode): array
    {
        try {
            DB::beginTransaction();
            $order = $this->repository->first(
                criteria: function($query) use ($orderCode){
                    $query->with('user', 'items.productVariant.product', 'items.productVariant.inventory')
                        ->where('order_code', $orderCode)
                        ->where('status', 1)
                        ->where('user_id', authPayload('sub'))
                        ->whereDoesntHave('payment')
                        ->whereDoesntHave('shipping')
                        ->sharedLock();
                }
            );

            if(!$order) {
                throw new RuntimeException('Order not found, already finalized, or cannot be processed.');
            }

            $shippingCreated = $order->shipping()->create(Arr::only($data, ['recipient_name', 'phone', 'province', 'district', 'ward', 'street', 'postal_code', 'note']));
            $order->setRelation('shipping', $shippingCreated);

            $isStripePayment = $data['payment_method'] === 'stripe';
            $paymentCreated = $order->payment()->create([
                'user_id' => $order->user_id,
                'method' => $isStripePayment ? PaymentMethod::CREDIT_CARD : $data['payment_method'],
                'status' => 0,
                'amount' => $order->total_amount,
            ]);
            $order->setRelation('payment', $paymentCreated);

            $checkoutSession = null;
            $dataResponse = [
                'order' => $order,
                'payment_method' => $data['payment_method']
            ];

            if($data['payment_method'] !== PaymentMethod::CASH->value) {
                $checkoutSession = $this->stripeService->createCheckoutSession($order, null, $isStripePayment);

                if(!$checkoutSession['success']) {
                    throw new RuntimeException($checkoutSession['error']);
                }

                $dataResponse['checkout_session'] = Arr::except($checkoutSession, ['success']);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Order finalized successfully.',
                'data' => $dataResponse
            ];

        }catch(QueryException $dbException) {
            DB::rollBack();
            Log::error('Database error during order finalization', ['error' => $dbException, 'order_code' => $orderCode]);

            return [
                'success' => false,
                'message' => 'An internal error occurred while finalizing the order.',
            ];

        }catch(Throwable $businessException) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $businessException->getMessage(),
            ];
        }
    }
}
