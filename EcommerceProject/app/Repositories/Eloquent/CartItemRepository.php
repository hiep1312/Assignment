<?php

namespace App\Repositories\Eloquent;

use App\Models\CartItem;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
{
    public function getModel()
    {
        return CartItem::class;
    }

    public function getAvailableByCartIds(array $cartIds, $useSharedLock = false)
    {
        $placeholdersCartIds = implode(',', array_fill(0, count($cartIds), '?'));

        return DB::table('carts', 'c')
            ->join('cart_items as ci', 'ci.cart_id', '=', 'c.id')
            ->join('product_variants as pv', 'pv.id', '=', 'ci.product_variant_id')
            ->join('product_variant_inventories as pvi', 'pvi.variant_id', '=', 'pv.id')
            ->where('c.user_id', authPayload('sub', null, false) ?? Auth::id())
            ->where('c.status', 1)
            ->where('c.expires_at', '>', now())
            ->whereIn('c.id', $cartIds)
            ->where('pv.status', 1)
            ->whereNull('pv.deleted_at')
            ->whereraw(<<<SQL
            pvi.stock >= (
                SELECT COALESCE(SUM(ci2.quantity), 0) FROM cart_items ci2
                INNER JOIN `carts` as `c2` ON `c2`.`id` = `ci2`.`cart_id`
                WHERE `c2`.`user_id` = `c`.`user_id`
                AND `c2`.`id` IN ({$placeholdersCartIds})
                AND `ci2`.`product_variant_id` = `pv`.`id`
            )
            SQL, $cartIds)
            ->select('ci.*', 'pvi.stock', 'pvi.sold_number')
            ->when($useSharedLock && DB::transactionLevel(), fn($query) => $query->sharedLock())
            ->get();
    }
}
