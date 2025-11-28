<?php

namespace App\Services;

use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Repositories\Contracts\CartRepositoryInterface;

class CartItemService
{
    public function __construct(
        protected CartItemRepositoryInterface $repository,
        protected CartRepositoryInterface $cartRepository
    ){}

    public function create(array $data, string $cartId): array
    {
        $availableCart = $this->repository->first(
            criteria: function($query) use ($cartId, $data){
                $query->with('items', function($subQuery) use ($data){
                    $subQuery->whereHas('productVariant', function($productVariantQuery) use ($data){
                        $productVariantQuery->where('sku', $data['sku']);
                    });
                });

                $query->where('id', $cartId)
                    ->where('status', 1)
                    ->where('expires_at', '>', now())
                    ->when(...CartService::userQueryConditions());
            }
        );
        $cartItem = null;

        if(!$availableCart) {
            return [
                'success' => false,
                'message' => 'Cart not found or expired.'
            ];

        }elseif($availableCart->items->isNotEmpty()) {
            $cartItem = $availableCart->items->first();
            $newQuantity = $cartItem->quantity + $data['quantity'];

            $cartItem->update([
                'quantity' => $newQuantity,
            ]);

        }else {
            $this->repository->createByVariantSku(
                attributes: [
                    'cart_id' => $availableCart->id,
                    'quantity' => $data['quantity']
                ],
                sku: $data['sku'],
                createdModel: $cartItem
            );
        }

        return [
            'success' => (bool) $cartItem,
            'message' => $cartItem
                ? 'Cart item created successfully.'
                : 'Failed to create cart item.',
            'data' => $cartItem
        ];
    }
}
