<?php

namespace App\Livewire\Admin\Orders;

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
    public function reloadDetail(){
        $this->lockReloadDetail = time();
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

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        return view('admin.pages.orders.order-index', compact('orders'));
    }
}
