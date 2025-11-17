<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductVariant;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class ProductVariantRepository extends BaseRepository implements ProductVariantRepositoryInterface
{
    public function getModel()
    {
        return ProductVariant::class;
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
            $createdModel = $this->model->where('sku', $fillableData['sku'])->first();
        }

        return $insertedRows;
    }
}
