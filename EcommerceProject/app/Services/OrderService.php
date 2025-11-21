<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        protected OrderRepositoryInterface $repository
    ){}

    protected function prepareTotalAmountUpdate(int $orderId): array
    {
        return [
            'total_amount' => DB::raw(<<<SQL
                (SELECT SUM(oi.quantity * oi.price)
                FROM order_items oi
                WHERE oi.order_id = {$orderId})
            SQL)
        ];
    }

    public function updateTotalAmount(Order|int $order): bool
    {
        $orderId = $order instanceof Order ? $order->id : $order;

        return $this->repository->update(
            idOrCriteria: $orderId,
            attributes: $this->prepareTotalAmountUpdate($orderId),
            rawEnabled: true
        );
    }

    public function update(array $data, string $orderCode): array
    {
        $transitionResult = $this->processStatusTransition($data, $orderCode);
        if($transitionResult){
            $transitionResult->query()
                ->where($transitionResult->getKeyName(), $transitionResult->getKey())
                ->update(array_merge($data, $this->prepareTotalAmountUpdate($transitionResult->id)));
            $transitionResult = $transitionResult->fresh();
        }

        return [(bool) $transitionResult, $transitionResult];
    }

    public function delete(string $orderCode): array|false
    {
        ['role' => $role, 'sub' => $userId] = authPayload();

        if($role === UserRole::ADMIN->value){
            $isDeleted = $this->repository->delete(
                idOrCriteria: fn($query) => $query->where('order_code', $orderCode)
            );

            return [(bool) $isDeleted];
        }

        $orderWithPayment = $this->repository->first(
            criteria: function($query) use ($orderCode, $userId){
                $query->with('payment')
                    ->where('order_code', $orderCode)
                    ->where('user_id', $userId);
            },
        );

        if(!$orderWithPayment) [false];
        elseif(!$orderWithPayment->canBeCancelled()) return false;

        $isDeleted = $orderWithPayment->forceDelete();

        return [(bool) $isDeleted];
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
            $cancellationStatuses = [OrderStatus::FAILED, OrderStatus::BUYER_CANCEL, OrderStatus::ADMIN_CANCEL];
            $attributes = array_merge($attributes, [
                'customer_note' => ($order->allowCustomerNote() && isset($attributes['customer_note']))
                    ? $attributes['customer_note']
                    : null,
                'admin_note' => ($role === UserRole::ADMIN->value && $order->allowAdminNote()) && isset($attributes['admin_note'])
                    ? $attributes['admin_note']
                    : null,
            ]);

            if(isset($attributes['status']) && !$order->isCancelled && !$order->isFinalized){
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

                if(in_array($newStatus, $allowedTransitions, true)){
                    $isCancellationStatus = in_array($newStatus, $cancellationStatuses, true);
                    $attributes = array_merge($attributes, [
                        $newStatus->timestampColumn() => now(),
                        'cancel_reason' => ($isCancellationStatus && isset($attributes['cancel_reason'])) ? $attributes['cancel_reason'] : null,
                    ]);
                }else {
                    return false;
                }
            }else{
                unset($attributes['status']);
            }
        }

        return $order;
    }
}
