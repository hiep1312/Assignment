<?php

namespace App\Services;

use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

class CartService
{
    public function __construct(
        protected CartRepositoryInterface $repository,
        protected CartItemRepositoryInterface $cartItemRepository,
        protected ProductVariantRepositoryInterface $productVariantRepository
    ){}

    public function create(array $data): array
    {
        $availableVariants = $this->productVariantRepository->find(
            idOrCriteria: function($query) use ($data){
                $query->with('inventory:variant_id,stock')
                    ->whereIn('sku', array_column($data, 'sku'))
                    ->where('status', 1)
                    ->whereHas('inventory', fn($subQuery) => $subQuery->where('stock', '>', 0));
            },
            columns: ['id', 'sku', 'price', 'discount']
        );

        if(!$availableVariants){
            return [
                'success' => false,
                'message' => 'No available products found for the requested SKUs or all are out of stock.'
            ];
        }

        $cartData = [
            'status' => 1
        ];

        if(Auth::guard('jwt')->check()){
            $cartData = array_merge($cartData, [
                'user_id' => authPayload('sub', -1, false),
                'expires_at' => now()->addDays(5)
            ]);
        }else {
            $cartGuestToken = Str::uuid();
            $cartData = array_merge($cartData, [
                'guest_token' => $cartGuestToken,
                'expires_at' => now()->addDay()
            ]);

            Cookie::queue('guest_token', $cartGuestToken, 60 * 24, httpOnly: true);
        }

        $createdCart = $this->repository->create($cartData);
        $cartItemsPayload = [];
        $outOfStockSkus = [];
        $skuQuantities = array_column($data, 'quantity', 'sku');

        foreach($availableVariants as $variant){
            $quantity = $skuQuantities[$variant->sku];
            $exceedsStock = $quantity > $variant->inventory->stock;

            $cartItemsPayload[] = [
                'product_variant_id' => $variant->id,
                'quantity' => $exceedsStock ? $variant->inventory->stock : $quantity,
                'price' => $variant->price,
            ];

            if($exceedsStock){
                $outOfStockSkus[] = $variant->sku;
            }
        }

        $createdCartItems = $createdCart->items()->createMany($cartItemsPayload);
        $createdCart->setRelation('items', $createdCartItems);

        return [
            'success' => true,
            'message' => empty($outOfStockSkus)
                ? 'Cart created successfully.'
                : 'Cart created with some items limited by stock.',
            'data' => $createdCart,
            'insufficient_stock_skus' => $outOfStockSkus
        ];
    }

    public function update(array $data, string $id): array
    {
        try {
            $availableCart = $this->repository->first(
                criteria: function($query) use ($id) {
                    $query->with('items')
                        ->where('id', $id)
                        ->where('status', 1)
                        ->where('expires_at', '>', now())
                        ->when(...self::userQueryConditions());
                },
            );

            if(!$availableCart) {
                throw new RuntimeException('Cart not found, expired, or not accessible.');
            }

            $requestedQuantities = array_column($data, 'quantity', 'item_id');
            $existingItems = $availableCart->items->keyBy('id');
            $validItemIds = array_intersect(array_keys($requestedQuantities), $existingItems->keys());
            $updatePayload = [];

            if(empty($validItemIds)) {
                throw new InvalidArgumentException('No valid cart items found to update.');
            }

            foreach($validItemIds as $itemId) {
                $requestedQuantity = $requestedQuantities[$itemId];
                $existingItem = $existingItems[$itemId];

                $updatePayload[] = [
                    'id' => $itemId,
                    'cart_id' => $availableCart->id,
                    'quantity' => $requestedQuantity,
                    'price' => $existingItem->price
                ];
                $existingItem->quantity = $requestedQuantity;
            }

            $this->cartItemRepository->upsert($updatePayload, ['id', 'cart_id']);
            $availableCart->setRelation('items', $existingItems->values());

            return [
                'success' => true,
                'message' => 'Cart updated successfully.',
                'data' => $availableCart
            ];

        }catch(Throwable $error) {
            return [
                'success' => false,
                'message' => $error->getMessage()
            ];
        }
    }

    public static function userQueryConditions(): array
    {
        return [
            Auth::guard('jwt')->check(),
            fn($subQuery) => $subQuery->where('user_id', authPayload('sub', -1, false)),
            fn($subQuery) => $subQuery->where('guest_token', request()->cookie('cart_guest', ''))
        ];
    }
}
