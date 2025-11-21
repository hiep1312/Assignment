<?php

namespace App\Services;

use App\Models\Order;
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
        $orderExistsWithShipping = $this->getOrderWithShipping($orderCode, false);

        if($orderExistsWithShipping) return false;

        $isCreated = $this->repository->createByOrderCode(
            attributes: $data,
            orderCode: $orderCode,
            createdModel: $createdShipping
        );

        return [(bool) $isCreated, $createdShipping];
    }

    public function update(array $data, string $orderCode): array|false
    {
        $orderWithShipping = $this->getOrderWithShipping($orderCode);

        if(!$orderWithShipping) return [false, null];
        elseif(!$orderWithShipping->allowCancel()) return false;

        $isUpdated = $orderWithShipping->shipping()->update($data);
        $updatedShipping = $orderWithShipping->shipping->fill($data);

        return [(bool) $isUpdated, $updatedShipping];
    }

    public function delete(string $orderCode): array|false
    {
        $orderWithShipping = $this->getOrderWithShipping($orderCode);

        if(!$orderWithShipping) return [false];
        elseif(!$orderWithShipping->allowCancel()) return false;

        $isDeleted = $orderWithShipping->shipping()->delete();

        return [(bool) $isDeleted];
    }

    protected function getOrderWithShipping(string $orderCode, bool $returnModel = true): Order|bool|null
    {
        $queryCallback = function($query) use ($orderCode, $returnModel){
            $returnModel && $query->with('shipping');

            $query->where('order_code', $orderCode)
                ->where('user_id', authPayload('sub'))
                ->whereHas('shipping');
        };

        return $returnModel
            ? $this->orderRepository->first(criteria: $queryCallback)
            : $this->orderRepository->exists(criteria: $queryCallback);
    }
}
