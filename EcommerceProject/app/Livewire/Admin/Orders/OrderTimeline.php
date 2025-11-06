<?php

namespace App\Livewire\Admin\Orders;

use App\Enums\OrderStatus;
use App\Events\MailSentEvent;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class OrderTimeline extends Component
{
    protected OrderRepositoryInterface $repository;
    public $order_id;

    public function boot(OrderRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function mount(int $order){
        $this->order_id = $order;
    }

    #[Computed]
    public function order(){
        return $this->repository->find(
            idOrCriteria: $this->order_id,
            columns: ['*'],
            throwNotFound: false
        );
    }

    public function confirmOrderTimelineStep(int $step){
        if(
            $this->order->isCancelled ||
            $this->order->status !== ($step - 1) ||
            $this->order->isFinal
        ) return;

        if(
            ($currentStatus = OrderStatus::tryFrom($step))
        ){
            $this->repository->update(
                idOrCriteria: $this->order_id,
                attributes: [
                    'status' => $currentStatus?->value,
                    $currentStatus?->timestampColumn() => now()
                ]
            );

            $this->notifyOrderStatusUpdated($currentStatus);
        }
    }

    public function resolveOrder(int $step){
        if(
            !$this->order->isFinal
        ) return;

        $currentStatus = OrderStatus::tryFrom($step);

        $this->repository->update(
            idOrCriteria: $this->order_id,
            attributes: [
                'status' => $currentStatus?->value,
                $currentStatus?->timestampColumn() => now(),
                'cancel_reason' => $currentStatus === OrderStatus::FAILED ? 'Customer refused to receive the order' : null
            ],
            updatedModel: $updatedOrder
        );

        if ($updatedOrder) {
            $updatedOrder->payment()
                ->where('method', 'cash')
                ->where('status', 0)
                ->update([
                    'status' => $currentStatus === OrderStatus::FAILED ? 2 : 1,
                    'paid_at' => $currentStatus === OrderStatus::FAILED ? null : now(),
                ]);
        }

        $this->notifyOrderStatusUpdated($currentStatus);
    }

    public function notifyOrderStatusUpdated(OrderStatus $currentStatus){
        $this->order->refresh();
        $this->dispatch('order-updated', $this->order);

        $statusName = ucwords(strtolower(str_replace("_", " ", $currentStatus->name)));
        session([
            'timeline-updated' => ['Status Updated', "The order status has been updated to: {$statusName}", now()->toIsoString()]
        ]);
    }

    public function getOrderTimelineStatus(?Carbon $doneAt){
        return match(true){
            is_object($doneAt) => "completed",
            $this->order->isCancelled => "failed",
            default => "pending",
        };
    }

    public function getOrderTimelineBtnLabel(?Carbon $doneAt, string $label){
        return match($this->getOrderTimelineStatus($doneAt)){
            "completed" => '<i class="fas fa-check-circle"></i> Confirmed',
            "failed" => '<i class="fas fa-times-circle"></i> Cancelled',
            default => '<i class="fas fa-check"></i> Confirm ' . $label,
        };
    }

    public function isOrderTimelineDisabled(?Carbon $doneAt, OrderStatus|int $step){
        $step = is_int($step) ? $step : $step->value;
        return in_array($this->getOrderTimelineStatus($doneAt), ['completed', 'failed'], true) || ($this->order->status !== ($step - 1));
    }

    #[Title('Order Timeline - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.orders.order-timeline');
    }
}
