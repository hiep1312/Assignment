<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;

class PaymentService
{
    public function __construct(
        protected PaymentRepositoryInterface $repository,
        protected OrderRepositoryInterface $orderRepository,
    ){}

    public function create(array $data, string $orderCode): array|false
    {
        $orderExistsWithPayment = $this->getOrderWithPayment($orderCode, false);

        if($orderExistsWithPayment) return false;

        $isCreated = $this->repository->createByOrderCode(
            attributes: $data,
            orderCode: $orderCode,
            createdModel: $createdPayment
        );

        return [(bool) $isCreated, $createdPayment];
    }

    protected function getOrderWithPayment(string $orderCode, bool $returnModel = true): Order|bool|null
    {
        $queryCallback = function($query) use ($orderCode, $returnModel){
            $returnModel && $query->with('payment');

            $query->where('order_code', $orderCode)
                ->where('user_id', authPayload('sub'))
                ->whereHas('payment');
        };

        return $returnModel
            ? $this->orderRepository->first(criteria: $queryCallback)
            : $this->orderRepository->exists(criteria: $queryCallback);
    }
}
