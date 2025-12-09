<?php

namespace App\Repositories\Eloquent;

use App\Enums\OrderStatus;
use App\Models\ProductReview;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductReviewRepository extends BaseRepository implements ProductReviewRepositoryInterface
{
    public function getModel()
    {
        return ProductReview::class;
    }

    public function hasUserPurchasedProduct($productId, $userId = null)
    {
        if(is_null($userId)) {
            $userId = authPayload(key: 'sub', throw: false) ?? Auth::id();
            if(is_null($userId)) return false;
        }

        return DB::table('orders', 'o')
            ->join('order_items as oi', 'oi.order_id', '=', 'o.id')
            ->join('product_variants as pv', 'pv.id', '=', 'oi.product_variant_id')
            ->where('pv.product_id', $productId)
            ->where('o.user_id', $userId)
            ->where('o.status', OrderStatus::COMPLETED->value)
            ->exists();
    }

    public function getFirstUserReview($userId = null)
    {
        if(is_null($userId)) {
            $userId = authPayload(key: 'sub', throw: false) ?? Auth::id();
            if(is_null($userId)) return null;
        }

        return DB::table($this->model->getTable(), 'pr')
            ->join('products as p', 'p.id', '=', 'pr.product_id')
            ->where('pr.user_id', $userId)
            ->whereNull('p.deleted_at')
            ->first('pr.*');
    }

    public function getRatingDistribution($productId)
    {
        return $this->model->query()
            ->select('rating', DB::raw('COUNT(*) AS total'))
            ->where('product_id', $productId)
            ->groupBy('rating')
            ->orderByDesc('rating')
            ->get();
    }

    public function getProductRatingDistribution()
    {
        return DB::table(DB::raw(<<<SQL
                (SELECT product_id, TRUNCATE(AVG(rating), 0) AS average_rating
                FROM {$this->model->getTable()}
                GROUP BY product_id) AS rating_summary
            SQL))
            ->selectRaw(<<<SQL
                average_rating AS rating, JSON_ARRAYAGG(product_id) AS product_ids, COUNT(*) AS total_products
            SQL)
            ->groupBy('average_rating')
            ->orderByDesc('average_rating')
            ->get();
    }
}
