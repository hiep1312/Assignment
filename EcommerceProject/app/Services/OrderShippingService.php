<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderShippingService
{
    public function __construct(
        protected OrderRepositoryInterface $orderRepository,
    ){}

    public function existsWithoutShipping(string $orderCode): bool
    {
        return $this->orderRepository->exists(
            criteria: function($query) use ($orderCode){
                $query->where('order_code', $orderCode)
                    ->where('user_id', authPayload('sub'))
                    ->whereDoesntHave('shipping');
            }
        );
    }
}
