<?php

namespace App\Repositories\Eloquent;

use App\Models\OrderShipping;
use App\Repositories\Contracts\OrderShippingRepositoryInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class OrderShippingRepository extends BaseRepository implements OrderShippingRepositoryInterface
{
    public function getModel()
    {
        return OrderShipping::class;
    }

    public function createByOrderCode(array $attributes, $orderCode, &$createdModel = null)
    {
        $fillableData = Arr::only($attributes, $this->model->getFillable());
        unset($fillableData['order_id']);

        $insertedRows = DB::table($this->model->getTable())->insertUsing(
            ['order_id', ...array_keys($fillableData), 'created_at', 'updated_at'],
            DB::table('orders')->selectRaw("id, ". str_repeat("?, ", count($fillableData)) ."NOW(), NOW()", array_values($fillableData))
                ->where('order_code', $orderCode)->limit(1)
        );

        if(func_num_args() > 2 && (bool) $insertedRows){
            $createdModel = $this->model->whereHas('order', fn($subQuery) => $subQuery->where('order_code', $orderCode))
                ->latest($this->model->getKeyName())->first();
        }

        return $insertedRows;
    }
}
