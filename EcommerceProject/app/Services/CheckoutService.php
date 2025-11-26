<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductVariantInventoryRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class CheckoutService
{
    public function __construct(
        protected OrderRepositoryInterface $repository,
        protected ProductVariantRepositoryInterface $variantRepository,
        protected ProductVariantInventoryRepositoryInterface $variantInventoryRepository,
        protected CartItemRepositoryInterface $cartItemRepository
    ){}

    public function create(array $data): array
    {
        try {
            DB::beginTransaction();
            $isCartCheckout = true;
            $cartData = [];

            if(!empty($data['carts']) && is_array($data['carts'])) {
                $temp = $this->cartItemRepository->getAvailableByCartIds($data['carts']);

                if($temp->isEmpty()) {
                    throw new RuntimeException('Cart items are unavailable or does not have sufficient stock.');
                }
            }else {
                $variant = $this->variantRepository->first(
                    criteria: function($query) use ($data){
                        $query->with('inventory:stock,sold_number');

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

                $isCartCheckout = false;
                $cartData[$variant->sku] = [
                    'quantity' => $data['quantity'],
                    'price' => $variant->discount ?? $variant->price
                ];
            }

            $totalAmount = 0;
            $shippingFee = 20_000;
            $orderItemsPayload = [];
            $stockAdjustments = [];

            if($isCartCheckout){

            }else {
                $this->prepareOrderItem($variant, $cartData, $totalAmount, $orderItemsPayload, $stockAdjustments);
            }

            $orderCreated = $this->repository->create([
                'user_id' => authPayload('sub'),
                'order_code' => (string) Str::uuid(),
                'total_amount' => $totalAmount,
                'shipping_fee' => $shippingFee,
                'status' => 1,
            ]);

            $orderItemsCreated = $orderCreated->items()->createMany($orderItemsPayload);
            $orderCreated->setRelation('items', $orderItemsCreated);
            $this->variantInventoryRepository->upsert($stockAdjustments, ['variant_id']);
            DB::commit();

            return [
                'success' => true,
                'message' => 'Order created successfully.',
                'data' => $orderCreated
            ];

        }catch(RuntimeException $businessException) {
            DB::rollBack();

            return [
                'success' => false,
                'message' => $businessException->getMessage(),
            ];

        }catch(QueryException $dbException) {
            DB::rollBack();
            Log::error('Database error during order creation', ['error' => $dbException]);

            return [
                'success' => false,
                'message' => 'An internal error occurred while creating the order.',
            ];
        }
    }

    protected function prepareOrderItem(
        ProductVariant $variant,
        array $cartData,
        int &$totalAmount,
        array &$orderItemsData,
        array &$inventoryUpdates
    ): void {
        $price = (int) $cartData[$variant->sku]['price'];
        $quantity = (int) $cartData[$variant->sku]['quantity'] ?? 1;

        $totalAmount += $price * $quantity;
        $orderItemsData[] = [
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
            'price' => $price
        ];
        $inventoryUpdates[] = [
            'variant_id' => $variant->id,
            'stock' => $variant->inventory->stock - $quantity,
            'sold_number' => $variant->inventory->sold_number + $quantity
        ];
    }
}
