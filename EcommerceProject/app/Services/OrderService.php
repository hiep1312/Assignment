<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $repository
    ){}

    public function update(array $data, string $orderCode): array|false
    {
        $transitionResult = $this->processStatusTransition($data, $orderCode);

        if($transitionResult && !empty($data)){
            $transitionResult->update($data);
        }

        return is_bool($transitionResult) ? false : [(bool) $transitionResult, $transitionResult];
    }

    protected function processStatusTransition(array &$attributes, string $orderCode): Order|false|null
    {
        ['role' => $role, 'sub' => $userId] = authPayload();
        $order = $this->repository->first(
            criteria: fn($query) => $query->where('order_code', $orderCode)
                ->when($role === UserRole::USER->value, fn($query) => $query->where('user_id', $userId)),
            columns: ['*'],
            throwNotFound: false
        );

        if($order){
            $currentStatus = OrderStatus::tryFrom($order->status);

            if(!$order->allowCustomerNote()) {
                unset($attributes['customer_note']);
            }

            if(!($role === UserRole::ADMIN->value && $order->allowAdminNote())){
                unset($attributes['admin_note']);
            }

            if(isset($attributes['status']) && !$order->isCancelled && !$order->isFinalized) {
                $newStatus = OrderStatus::tryFrom($attributes['status']);
                $cancelStatus = ($role === UserRole::ADMIN->value ? OrderStatus::ADMIN_CANCEL : OrderStatus::BUYER_CANCEL);

                $allowedTransitions = match($currentStatus){
                    OrderStatus::NEW => [OrderStatus::CONFIRMED, $cancelStatus],
                    OrderStatus::CONFIRMED => [OrderStatus::PROCESSING, $cancelStatus],
                    OrderStatus::PROCESSING => [OrderStatus::SHIPPED, $cancelStatus],
                    OrderStatus::SHIPPED => [OrderStatus::DELIVERED, $order->allowCancel() ? $cancelStatus : null],
                    OrderStatus::DELIVERED => [OrderStatus::COMPLETED, OrderStatus::FAILED],
                    default => []
                };

                if(in_array($newStatus, $allowedTransitions, true)) {
                    $isCancellationStatus = in_array($newStatus, [OrderStatus::FAILED, OrderStatus::BUYER_CANCEL, OrderStatus::ADMIN_CANCEL], true);

                    $attributes = array_merge($attributes, [
                        $newStatus->timestampColumn() => now(),
                        'cancel_reason' => ($isCancellationStatus && isset($attributes['cancel_reason'])) ? $attributes['cancel_reason'] : null,
                    ]);
                }else {
                    return false;
                }
            }else {
                unset($attributes['status'], $attributes['cancel_reason']);
            }
        }

        return $order;
    }
}
