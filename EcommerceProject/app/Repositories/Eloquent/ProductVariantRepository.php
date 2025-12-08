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

    public function getPriceRange()
    {
        return $this->model->query()
            ->selectRaw(<<<SQL
                MIN(COALESCE(discount, price)) AS min_price, MAX(COALESCE(discount, price)) AS max_price
            SQL)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->first();
    }
}
