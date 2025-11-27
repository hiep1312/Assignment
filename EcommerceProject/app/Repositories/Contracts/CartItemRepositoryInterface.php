<?php

namespace App\Repositories\Contracts;

interface CartItemRepositoryInterface extends RepositoryInterface
{
    /**
     * Retrieve available cart items for the given cart IDs.
     *
     * @param array $cartIds Array of cart IDs to check availability for
     * @param bool $useSharedLock Whether to apply a shared lock during the query (default: false).
     *                            Only applied when inside a database transaction.
     * @return \Illuminate\Support\Collection Collection of available cart items with stock information
     *
     * @throws \InvalidArgumentException If $cartIds is empty
     */
    public function getAvailableByCartIds(array $cartIds, $useSharedLock = false);

    /**
     * Create a new record by product variant SKU with associated attributes.
     *
     * @param array $attributes The attributes for the new record. Only fillable fields are used.
     *                         The 'product_variant_id' and 'price' keys are explicitly excluded if present.
     * @param string $sku The unique SKU identifier of the product variant to associate with.
     * @param \Illuminate\Database\Eloquent\Model|null $createdModel Optional reference parameter.
     *                         If provided, it will be populated with the newly created model instance retrieved by cart_id and matching variant SKU.
     *
     * @return int The number of rows inserted (typically 1 on success, 0 if variant not found).
     *
     * @throws \Illuminate\Database\QueryException If the database operation fails (e.g., constraint violation).
     */
    public function createByVariantSku(array $attributes, $sku, &$createdModel = null);
}
