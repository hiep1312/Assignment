<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductVariantInventory;
use App\Repositories\Contracts\ProductVariantInventoryRepositoryInterface;

class ProductVariantInventoryRepository extends BaseRepository implements ProductVariantInventoryRepositoryInterface
{
    public function getModel()
    {
        return ProductVariantInventory::class;
    }
}
