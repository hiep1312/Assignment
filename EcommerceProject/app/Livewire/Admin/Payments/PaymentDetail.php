<?php

namespace App\Livewire\Admin\Payments;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class PaymentDetail extends Component
{
    protected PaymentRepositoryInterface $repository;
    public $order_id;

    public function boot(PaymentRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function mount(int $order){
        $this->order_id = $order;
    }

    #[Title('Payment Detail - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $payment = $this->repository->first(
            criteria: fn($query) => $query->where('order_id', $this->order_id),
            columns: ['*'],
            throwNotFound: false
        );

        return view('admin.pages.payments.payment-detail', compact('payment'));
    }
}
