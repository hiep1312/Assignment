<?php

namespace App\Livewire\Admin\Orders;

use App\Enums\OrderStatus;
use App\Events\MailSentEvent;
use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class OrderIndex extends Component
{
    use WithPagination;

    public bool $isTrashed = false;
    protected OrderRepositoryInterface $repository;

    public string $search = '';
    public ?int $status = null;
    public string $paymentMethod = '';
    public array $selectedRecordIds = [];
    public ?int $recordDetail = null;
    public int $lockReloadDetail = 0;

    public function boot(OrderRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function updatedIsTrashed(){
        $this->reset('selectedRecordIds');
        $this->js(<<<JS
            new Promise(resolve => setTimeout(updateSelectAllState));
        JS);
    }

    public function resetFilters(){
        $this->reset('search', 'status', 'paymentMethod');
        $this->resetPage();
    }

    #[On('order.deleted')]
    public function softDelete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[On('order.restored')]
    public function restore(?int $id = null){
        $this->repository->restore(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds)),
        );
    }

    #[On('order.forceDeleted')]
    public function forceDelete(?int $id = null){
        $this->repository->forceDelete(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[On('order-updated')]
    public function reloadDetail(?Order $order = null){
        $this->lockReloadDetail = time();
        if($order){
            event(new MailSentEvent(
                $order->isFinalized
                    ? ($order->status === OrderStatus::COMPLETED->value ? 1 : 2)
                    : ($order->isCancelled ? 2 : 3),
                $order->user_id,
                $order
            ));
        }
    }

    #[Title('Order List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $orders = $this->repository->getAll(criteria: function(&$query) {
            $query->with([
                'user' => fn($subQuery) => $subQuery->withTrashed(),
                'items.productVariant' => function($subQueryVariant){
                    $subQueryVariant->withTrashed();

                    $subQueryVariant->with('product', fn($subQueryProduct) => $subQueryProduct->withTrashed());
                },
                'payment'
            ]);

            if($this->isTrashed) $query->onlyTrashed();

            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('order_code', '%'. trim($this->search) .'%')
                        ->orWhereLike('customer_note', '%'. trim($this->search) .'%')
                        ->orWhereHas('user', function($subQueryUser){
                            $subQueryUser->whereLike('email', '%'. trim($this->search) .'%')
                                ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'. trim($this->search) .'%']);
                        });
                });
            })
            ->when(
                $this->status !== null,
                fn($innerQuery) => $innerQuery->where('status', $this->status)
            )->when(
                $this->paymentMethod,
                fn($innerQuery) => $innerQuery->whereHas('payment', function($subQuery) {
                    $subQuery->where('method', $this->paymentMethod);
                })
            );

            $query->whereHas('payment');
            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        $statistic = [
            [
                'title' => 'New Orders',
                'value' => $this->repository->count(fn($query) => $query->where('status', 1)),
                'icon' => 'fas fa-bell',
            ],
            [
                'title' => 'Revenue (30 days)',
                'value' => number_format($this->repository->sum('total_amount', function($query){
                    $query->where('status', 6)
                        ->where('completed_at', '>=', now()->subDays(30));
                }), 0, '.', '.') . 'đ',
                'icon' => 'fas fa-dollar-sign',
            ],
            [
                'title' => 'Completed (30 days)',
                'value' => $this->repository->count(function($query){
                    $query->where('status', 6)
                        ->where('completed_at', '>=', now()->subDays(30));
                }),
                'icon' => 'fas fa-check-circle',
            ],
            [
                'title' => 'Cancelled (30 days)',
                'value' => $this->repository->count(function($query){
                    $query->whereIn('status', [7, 8, 9])
                        ->where('cancelled_at', '>=', now()->subDays(30));
                }),
                'icon' => 'fas fa-times-circle',
            ],
        ];

        return view('admin.pages.orders.order-index', compact('orders', 'statistic'));
    }
}
