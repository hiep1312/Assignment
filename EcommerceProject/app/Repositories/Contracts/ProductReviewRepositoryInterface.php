<?php

namespace App\Repositories\Contracts;

interface ProductReviewRepositoryInterface extends RepositoryInterface
{
    /**
     * Create a new record by product slug with associated attributes.
     *
     * @param array $attributes The attributes for the new record. Only fillable fields are used.
     *                         The 'product_id' key is explicitly excluded if present.
     * @param string $slug The unique slug identifier of the product to associate with.
     * @param \Illuminate\Database\Eloquent\Model|null $createdModel Optional reference parameter.
     *                         If provided, it will be populated with the newly created
     *                         model instance retrieved by user_id and latest primary key.
     *
     * @return int The number of rows inserted (typically 1 on success, 0 if product not found).
     *
     * @throws \Illuminate\Database\QueryException If the database operation fails (e.g., constraint violation).
     */
    public function createByProductSlug(array $attributes, $slug, &$createdModel = null);
}
