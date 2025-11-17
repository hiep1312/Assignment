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
}
