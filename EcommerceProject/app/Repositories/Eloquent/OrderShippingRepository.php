<?php

namespace App\Repositories\Eloquent;

use App\Models\OrderShipping;
use App\Repositories\Contracts\OrderShippingRepositoryInterface;

class OrderShippingRepository extends BaseRepository implements OrderShippingRepositoryInterface
{
    public function getModel()
    {
        return OrderShipping::class;
    }
}
