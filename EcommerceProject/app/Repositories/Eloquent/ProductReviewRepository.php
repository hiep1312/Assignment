<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductReview;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductReviewRepository extends BaseRepository implements ProductReviewRepositoryInterface
{
    public function getModel()
    {
        return ProductReview::class;
    }

    public function createByProductSlug(array $attributes, $slug, &$createdModel = null)
    {
        $fillableData = Arr::only($attributes, $this->model->getFillable());
        unset($fillableData['product_id']);

        $insertedRows = DB::table($this->model->getTable())->insertUsing(
            ['product_id', ...array_keys($fillableData), 'created_at', 'updated_at'],
            DB::table('products')->selectRaw("id, ". str_repeat("?, ", count($fillableData)) ."NOW(), NOW()", array_values($fillableData))
                ->where('slug', $slug)->limit(1)
        );

        if(func_num_args() > 2 && (bool) $insertedRows){
            $createdModel = $this->model->where('user_id', $fillableData['user_id'])
                ->latest($this->model->getKeyName())->first();
        }

        return $insertedRows;
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
