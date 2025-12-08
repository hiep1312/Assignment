<?php

namespace App\Services;

use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductVariantService
{
    public function __construct(
        protected ProductVariantRepositoryInterface $repository,
    ){}

    public function create(array $data, string $productId): array
    {
        try {
            $createdVariant = $this->repository->create(
                attributes: array_merge(
                    $data,
                    ['product_id' => $productId, 'status' => $data['stock'] > 0 ? $data['status'] : 0]
                )
            );

            $createdInventory = $createdVariant->inventory()->create(['stock' => $data['stock']]);

            return [true, $createdVariant, $createdInventory];

        }catch(QueryException $queryException) {
            return [false, null, null];
        }
    }

    public function update(array $data, string $sku): array
    {
        $inventoryKeys = ['stock', 'reserved', 'sold_number'];
        $statusPatch = isset($data['status']) ? (
                (!isset($data['stock']) || $data['stock'] > 0) ? $data['status'] : 0
            ) : (
                isset($data['stock']) ? DB::raw("CASE WHEN status != 0 AND {$data['stock']} <= 0 THEN 0 ELSE status END") : null
            );
        $isUpdated = $this->repository->update(
            idOrCriteria: fn($query) => $query->with('inventory')->where('sku', $sku),
            attributes: array_merge(
                Arr::except($data, $inventoryKeys),
                is_null($statusPatch) ? [] : ['status' => $statusPatch]
            ),
            rawEnabled: true,
            updatedModel: $updatedVariant
        );

        $updatedVariant = $updatedVariant->first();
        $updatedInventoryData = Arr::only($data, $inventoryKeys);
        $oldInventoryData = Arr::only($updatedVariant?->inventory->toArray() ?? [], $inventoryKeys);
        $updatedInventory = $updatedVariant ? array_merge(
            ['variant_id' => $updatedVariant->id],
            $oldInventoryData,
            $updatedInventoryData
        ) : null;

        if($isUpdated && !empty(array_diff_assoc($updatedInventoryData, $oldInventoryData))){
            $updatedVariant->inventory()->update($updatedInventoryData);
        }

        return [(bool) $isUpdated, $updatedVariant, $updatedInventory];
    }
}
