<?php

namespace App\Repositories\Eloquent;

use App\Models\Cart;
use App\Repositories\Contracts\CartRepositoryInterface;
use Illuminate\Support\Facades\DB;

class CartRepository extends BaseRepository implements CartRepositoryInterface
{
    public function getModel()
    {
        return Cart::class;
    }

    public function refreshAndCleanupCarts($extendValue, $extendUnit = 'DAY')
    {
        return DB::statement(<<<SQL
            CALL refresh_and_cleanup_carts(?, ?)
        SQL, [$extendValue, $extendUnit]);
    }
}
