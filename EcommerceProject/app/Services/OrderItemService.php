<?php

namespace App\Services;

use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderItemService
{
    public function __construct(
        protected ProductVariantRepositoryInterface $variantRepository
    ){}

    public function prepareOrderItem(string $sku, int $quantity): ?array
    {
        $variant = $this->variantRepository->first(
            criteria: function($query) use ($sku, $quantity){
                $query->where('sku', $sku)
                    ->where('status', 1)
                    ->whereHas('inventory', fn($subQuery) => $subQuery->where('stock', '>=', $quantity));
            },
            columns: ['id', 'price', 'discount', 'status']
        );

        if(!$variant) return null;

        $orderItemData = [
            'product_variant_id' => $variant->id,
            'quantity' => $quantity,
            'price'=> $variant->discount ?? $variant->price,
        ];

        $isUpdated = $this->adjustInventoryStock($variant, $quantity);

        return (bool) $isUpdated ? $orderItemData : null;
    }

    public function adjustOrderItemQuantity(int $variantId, int $oldQuantity, int $newQuantity): array|true|null
    {
        $difference = $newQuantity - $oldQuantity;
        if($difference === 0) return true;

        $variant = $this->variantRepository->first(
            criteria: function($query) use ($variantId, $difference){
                $query->where('id', $variantId)
                    ->when(
                        $difference > 0,
                        fn($query) => $query->whereHas('inventory', fn($subQuery) => $subQuery->where('stock', '>=', $difference))
                    );
            },
            columns: ['id', 'price', 'discount', 'status']
        );

        if($variant && (bool) $this->adjustInventoryStock($variant, $difference)){
            return [
                'price'=> $variant->discount ?? $variant->price,
                'quantity' => $newQuantity
            ];
        }

        return null;
    }

    protected function adjustInventoryStock(ProductVariant $variant, int $quantity): bool
    {
        return $this->variantRepository->update(
            idOrCriteria: function($query) use ($variant, $quantity){
                $query->join('product_variant_inventories as pvi', "pvi.variant_id", '=', "{$variant->getTable()}.{$variant->getKeyName()}")
                    ->where('pvi.stock', '>=', $quantity)
                    ->where("{$variant->getTable()}.{$variant->getKeyName()}", $variant->id);
            },
            attributes: [
                "{$variant->getTable()}.status" => DB::raw("CASE WHEN (pvi.stock - {$quantity}) <= 0 THEN 0 ELSE 1 END"),
                "pvi.stock" => DB::raw("pvi.stock - {$quantity}"),
            ]
        );
    }
}
