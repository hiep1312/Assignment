<?php

namespace App\Repositories\Eloquent;

use App\Models\CartItem;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartItemRepository extends BaseRepository implements CartItemRepositoryInterface
{
    public function getModel()
    {
        return CartItem::class;
    }

    public function getAvailableCartItems(array $cartItemIds = [], $useSharedLock = false)
    {
        $userId = authPayload('sub', null, false) ?? Auth::id();
        if(is_null($userId)) return collect();

        $subQueryCondition = '';
        if(!empty($cartItemIds)) {
            $placeholdersCartItemIds = implode(',', array_fill(0, count($cartItemIds), '?'));
            $subQueryCondition = "AND `ci2`.`id` IN ({$placeholdersCartItemIds})";
        }

        return DB::table('carts', 'c')
            ->join('cart_items as ci', 'ci.cart_id', '=', 'c.id')
            ->join('product_variants as pv', 'pv.id', '=', 'ci.product_variant_id')
            ->join('product_variant_inventories as pvi', 'pvi.variant_id', '=', 'pv.id')
            ->where('c.user_id', $userId)
            ->where('c.status', 1)
            ->where('c.expires_at', '>', now())
            ->when(
                !empty($cartItemIds),
                fn($query) => $query->whereIn('ci.id', $cartItemIds)
            )
            ->where('pv.status', 1)
            ->whereNull('pv.deleted_at')
            ->whereraw(<<<SQL
            pvi.stock >= (
                SELECT COALESCE(SUM(ci2.quantity), 0) FROM cart_items ci2
                INNER JOIN `carts` as `c2` ON `c2`.`id` = `ci2`.`cart_id`
                WHERE `c2`.`user_id` = `c`.`user_id` AND `ci2`.`product_variant_id` = `pv`.`id`
                {$subQueryCondition}
            )
            SQL, $cartItemIds)
            ->select('ci.*', 'pvi.stock', 'pvi.sold_number')
            ->when($useSharedLock && DB::transactionLevel(), fn($query) => $query->sharedLock())
            ->get();
    }

    public function createByVariantSku(array $attributes, $sku, &$createdModel = null)
    {
        $fillableData = Arr::only($attributes, $this->model->getFillable());
        unset($fillableData['product_variant_id'], $fillableData['price']);

        $insertedRows = DB::table($this->model->getTable())->insertUsing(
            ['product_variant_id', ...array_keys($fillableData), 'price', 'created_at', 'updated_at'],
            DB::table('product_variants')->selectRaw("id, ". str_repeat("?, ", count($fillableData)) ."price, NOW(), NOW()", array_values($fillableData))
                ->where('sku', $sku)->limit(1)
        );

        if(func_num_args() > 2 && (bool) $insertedRows){
            $createdModel = $this->model->where('cart_id', $fillableData['cart_id'])
                ->whereHas('productVariant', fn($query) => $query->where('sku', $sku))
                ->first();
        }

        return $insertedRows;
    }
}
