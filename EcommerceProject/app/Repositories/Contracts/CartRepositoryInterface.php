<?php

namespace App\Repositories\Contracts;

interface CartRepositoryInterface extends RepositoryInterface
{
    /**
     * Refresh and cleanup shopping carts by removing invalid items and extending cart expiration.
     *
     * @param int $extendValue The amount of time to extend cart expiration
     * @param string $extendUnit The time unit for extension (SECOND, MINUTE, HOUR, DAY, WEEK, MONTH, YEAR)
     *
     * @return bool Returns true if the procedure executed successfully, false otherwise
     *
     * @throws \Illuminate\Database\QueryException If the database operation fails
     */
    public function refreshAndCleanupCarts($extendValue, $extendUnit = 'DAY');
}
