<?php

namespace App\Repositories\Contracts;

interface CartItemRepositoryInterface extends RepositoryInterface
{
    /**
     * Retrieve available cart items for the authenticated user.
     *
     * @param array $cartItemIds Optional array of specific cart item IDs to check. If empty, checks all items in the user's active cart.
     *                           This allows for partial cart validation (e.g., checking only selected items during checkout).
     * @param bool $useSharedLock Whether to apply a shared lock during the query (default: false).
     *                            Only applied when inside a database transaction. Recommended for order placement to prevent race conditions.
     *
     * @return \Illuminate\Support\Collection Collection of available cart items with the following attributes:
     *                         - All cart_items table columns (ci.*)
     *                         - stock: Current available inventory (pvi.stock)
     *                         - sold_number: Total units sold (pvi.sold_number)
     *                         Returns empty collection if user is not authenticated.
     */
    public function getAvailableCartItems(array $cartItemIds = [], $useSharedLock = false)

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
