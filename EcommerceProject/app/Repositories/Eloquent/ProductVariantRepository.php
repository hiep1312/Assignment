<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;

class ProductVariantRepository extends BaseRepository implements ProductVariantRepositoryInterface
{
    public function getModel()
    {
        return ProductVariant::class;
    }
}
