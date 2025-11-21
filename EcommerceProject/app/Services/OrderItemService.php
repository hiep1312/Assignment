<?php

namespace App\Services;

use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderItemService
{
    public function __construct(
        protected OrderItemRepositoryInterface $repository,
        protected ProductVariantRepositoryInterface $variantRepository,
        protected OrderService $orderService
    ){}

    public function create(array $data, string $orderCode): array|false
    {
        $variant = $this->variantRepository->first(
            criteria: function($query) use ($data){
                $query->where('sku', $data['sku'])
                    ->where('status', 1)
                    ->whereHas('inventory', fn($subQuery) => $subQuery->where('stock', '>=', $data['quantity']));
            },
            columns: ['id', 'price', 'discount', 'status']
        );

        if(!$variant) return false;

        $orderItemData = [
            'product_variant_id' => $variant->id,
            'quantity' => $data['quantity'],
            'price'=> $variant->discount ?? $variant->price,
        ];

        $isUpdatedInventoryStock = $this->adjustInventoryStock($variant, $data['quantity']);
        $isCreated = $isUpdatedInventoryStock ? $this->repository->createByOrderCode(
            attributes: $orderItemData,
            orderCode: $orderCode,
            createdModel: $createdOrderItem
        ) : null;

        if(isset($createdOrderItem) && $createdOrderItem instanceof OrderItem){
            $this->orderService->updateTotalAmount($createdOrderItem->order_id);
        }

        return [(bool) $isCreated, $createdOrderItem ?? null];
    }

    public function update(array $data, string $orderCode, string $id): array|false
    {
        $currentItem = $this->repository->first(
            criteria: function($query) use ($orderCode, $id){
                $query->where('id', $id)
                    ->whereHas('order', function($subQuery) use ($orderCode){
                        $subQuery->where('order_code', $orderCode)
                            ->where('user_id', authPayload('sub'));
                    });
            },
            columns: ['*'],
        );

        if(!$currentItem) return [false, null];

        $difference = $data['quantity'] - $currentItem->quantity;
        if($difference === 0) return [true, $currentItem];

        $variant = $this->variantRepository->first(
            criteria: function($query) use ($id, $difference){
                $query->whereHas('orderItems', fn($subQuery) => $subQuery->where('id', $id))
                    ->when(
                        $difference > 0,
                        fn($query) => $query->whereHas('inventory', fn($subQuery) => $subQuery->where('stock', '>=', $difference))
                    );
            },
            columns: ['id', 'price', 'discount', 'status']
        );

        if($variant && (bool) $this->adjustInventoryStock($variant, $difference)){
            $attributes = [
                'price'=> $variant->discount ?? $variant->price,
                'quantity' => $data['quantity']
            ];

            $currentItem->update($attributes);
            $this->orderService->updateTotalAmount($currentItem->order_id);

            return [true, $currentItem];
        }

        return false;
    }

    public function delete(string $orderCode, string $id): bool
    {
        $currentItem = $this->repository->first(
            criteria: function($query) use ($orderCode, $id){
                $query->where('id', $id)
                    ->whereHas('order', function($subQuery) use ($orderCode){
                        $subQuery->where('order_code', $orderCode)
                            ->where('user_id', authPayload('sub'));
                    });
            },
            columns: ['order_id', 'product_variant_id', 'quantity'],
        );

        if(!$currentItem) return false;

        $this->adjustInventoryStock($currentItem->product_variant_id, -$currentItem->quantity);
        $this->orderService->updateTotalAmount($currentItem->order_id);
        $currentItem->delete();

        return true;
    }

    protected function adjustInventoryStock(ProductVariant|int $variant, int $quantity): bool
    {
        return $this->variantRepository->update(
            idOrCriteria: function($query) use ($variant, $quantity){
                $query->join('product_variant_inventories as pvi', "pvi.variant_id", '=', "product_variants.id")
                    ->when($quantity > 0, fn($query) => $query->where('pvi.stock', '>=', $quantity))
                    ->where("product_variants.id", $variant instanceof ProductVariant ? $variant->id : $variant);
            },
            attributes: [
                "product_variants.status" => DB::raw("CASE WHEN (pvi.stock - {$quantity}) <= 0 THEN 0 ELSE 1 END"),
                "pvi.stock" => DB::raw("pvi.stock - {$quantity}"),
            ],
            rawEnabled: true
        );
    }
}
