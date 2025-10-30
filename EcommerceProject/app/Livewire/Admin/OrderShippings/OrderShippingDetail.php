<?php

namespace App\Livewire\Admin\OrderShippings;

use App\Repositories\Contracts\OrderShippingRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class OrderShippingDetail extends Component
{
    protected OrderShippingRepositoryInterface $repository;
    public $order_id;

    public function boot(OrderShippingRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function mount(int $order){
        $this->order_id = $order;
    }

    #[Title('Order Shipping Detail - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $shipping = $this->repository->first(
            criteria: fn($query) => $query->where('order_id', $this->order_id),
            columns: ['*'],
            throwNotFound: false
        );

        return view('admin.pages.order-shippings.order-shipping-detail', compact('shipping'));
    }
}
