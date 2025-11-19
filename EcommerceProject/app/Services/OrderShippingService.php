<?php

namespace App\Services;

use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\OrderShippingRepositoryInterface;

class OrderShippingService
{
    public function __construct(
        protected OrderShippingRepositoryInterface $repository,
        protected OrderRepositoryInterface $orderRepository,
    ){}

    public function create(array $data, string $orderCode): array|false
    {
        $orderExistsWithoutShipping = $this->orderRepository->exists(
            criteria: function($query) use ($orderCode){
                $query->where('order_code', $orderCode)
                    ->where('user_id', authPayload('sub'))
                    ->whereDoesntHave('shipping');
            }
        );

        if(!$orderExistsWithoutShipping) return false;

        $isCreated = $this->repository->createByOrderCode(
            attributes: $data,
            orderCode: $orderCode,
            createdModel: $createdShipping
        );

        return [(bool) $isCreated, $createdShipping];
    }

    public function update(array $data, string $orderCode): array|false
    {
        $orderWithShipping = $this->orderRepository->first(
            criteria: function($query) use ($orderCode){
                $query->with('shipping');

                $query->where('order_code', $orderCode)
                    ->where('user_id', authPayload('sub'))
                    ->whereHas('shipping');
            },
        );

        if(!$orderWithShipping) return [false, null];
        elseif(!$orderWithShipping->canUpdateDependencies()) return false;

        $isUpdated = $orderWithShipping->shipping()->update($data);
        $updatedShipping = $orderWithShipping->shipping->fill($data);

        return [(bool) $isUpdated, $updatedShipping];
    }
}
