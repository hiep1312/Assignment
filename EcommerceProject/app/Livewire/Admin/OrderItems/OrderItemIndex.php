<?php

namespace App\Livewire\Admin\OrderItems;

use App\Repositories\Contracts\OrderItemRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class OrderItemIndex extends Component
{
    use WithPagination;

    protected OrderItemRepositoryInterface $repository;

    public $order_id;
    public string $search = '';
    public string $quantityRange = '';

    public function boot(OrderItemRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function mount(int $order){
        $this->order_id = $order;
    }

    public function resetFilters(){
        $this->reset('search', 'quantityRange');
        $this->resetPage();
    }

    #[Title('Order Item List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $orderItems = $this->repository->getAll(criteria: function(&$query) {
            $query->with('productVariant.product.mainImages');

            $query->when($this->search, function($innerQuery){
                $innerQuery->whereHas('productVariant', function($subQuery){
                    $subQuery->whereLike('name', '%'. trim($this->search) .'%')
                        ->orWhereHas('product', fn($subQueryProduct) => $subQueryProduct->whereLike('title', '%'. trim($this->search) .'%'));
                });
            })
            ->when(
                $this->quantityRange,
                fn($innerQuery) => $this->quantityRange === "11+"
                    ? $innerQuery->where('quantity', '>=', 11)
                    : $innerQuery->whereBetween('quantity', explode('-', $this->quantityRange))
            )->where('order_id', $this->order_id);

            $query->latest();
        }, perPage: 7, columns: ['*'], pageName: 'OrderItemsPage');

        return view('admin.pages.order-items.order-item-index', compact('orderItems'));
    }
}
