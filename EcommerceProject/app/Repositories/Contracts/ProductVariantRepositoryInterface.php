<?php

namespace App\Repositories\Contracts;

interface ProductVariantRepositoryInterface extends RepositoryInterface
{
    /**
     * Get the minimum and maximum price range for active products.
     *
     * @return \App\Models\ProductVariant|null Returns a ProductVariant model instance containing min_price and max_price properties, or null if no records match the criteria.
     */
    public function getPriceRange();
}
