<?php

namespace App\Repositories\Contracts;

interface PaymentRepositoryInterface extends RepositoryInterface
{
    /**
     * Create a new record by order code with associated attributes.
     *
     * @param array $attributes The attributes for the new record. Only fillable fields are used.
     *                          The 'order_id' key is explicitly excluded if present.
     * @param string $orderCode The unique order code to associate the new record with.
     * @param \Illuminate\Database\Eloquent\Model|null $createdModel Optional reference parameter.
     *                          If provided, it will be populated with the newly created
     *                          model instance retrieved by order_code and latest primary key.
     *
     * @return int The number of rows inserted (typically 1 on success, 0 if order not found).
     *
     * @throws \Illuminate\Database\QueryException If the database operation fails (e.g., constraint violation).
     */
    public function createByOrderCode(array $attributes, $orderCode, &$createdModel = null);
}
