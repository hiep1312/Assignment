<?php

namespace App\Services;

use App\Repositories\Contracts\CartRepositoryInterface;

class CartService
{
    public function __construct(
        protected CartRepositoryInterface $repository,
    ){}
}
