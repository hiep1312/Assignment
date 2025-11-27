<?php

namespace App\Services;

use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class CartService
{
    public function __construct(
        protected CartRepositoryInterface $repository,
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

        if(!$availableVariants) return [
            'success' => false,
            'message' => 'No available products found for the requested SKUs or all are out of stock.'
        ];

        $cartData = [
            'status' => 1
        ];

        if(Auth::guard('jwt')->check()){
            $cartData = array_merge($cartData, [
                'user_id' => authPayload('sub', -1, false),
                'expires_at' => now()->addDays(7)
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
        $availableCart = $this->repository->first(
            criteria: function($query) use ($id){
                $query->with('items');
            },
        );
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
