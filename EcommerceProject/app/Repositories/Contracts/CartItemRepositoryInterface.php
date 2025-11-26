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
}
