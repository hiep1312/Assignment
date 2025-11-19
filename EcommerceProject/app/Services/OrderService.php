<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $repository
    ){}

    public function updateOrder()
    {

    }
}
