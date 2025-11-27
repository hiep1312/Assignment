<?php

namespace App\Services;

use App\Models\Cart;
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
        $filterCartItemBySku = function($subQuery) use ($data) {
            $subQuery->whereHas('productVariant', function($productVariantQuery) use ($data){
                $productVariantQuery->where('sku', $data['sku']);
            });
        };

        $availableCart = $this->getAvailableCart($cartId, $filterCartItemBySku);
        $existingItem = null;

        if(!$availableCart) {
            return [
                'success' => false,
                'message' => 'Cart not found or expired.'
            ];

        }elseif($availableCart->items->isNotEmpty()) {
            $existingItem = $availableCart->items->first();
            $newQuantity = $existingItem->quantity + $data['quantity'];

            $existingItem->update([
                'quantity' => $newQuantity,
            ]);

        }else {
            $this->repository->createByVariantSku(
                attributes: array_merge($data, ['cart_id' => $availableCart->id]),
                sku: $data['sku'],
                createdModel: $existingItem
            );
        }

        return [
            'success' => (bool) $existingItem,
            'message' => $existingItem
                ? 'Cart item created successfully.'
                : 'Failed to create cart item.',
            'data' => $existingItem
        ];
    }

    public function update(array $data, string $cartId, string $id): array
    {

    }

    protected function getAvailableCart(string $cartId, callable $itemCondition): ?Cart
    {
        return $this->repository->first(
            criteria: function($query) use ($cartId, $itemCondition){
                $query->with('items', function($subQuery) use ($itemCondition){
                    $itemCondition($subQuery);
                });

                $query->where('id', $cartId)
                    ->where('status', 1)
                    ->where('expires_at', '>', now())
                    ->when(...CartService::userQueryConditions());
            }
        );
    }
}
