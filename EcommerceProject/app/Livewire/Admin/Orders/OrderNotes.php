<?php

namespace App\Livewire\Admin\Orders;

use App\Enums\OrderStatus;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

class OrderNotes extends Component
{
    protected OrderRepositoryInterface $repository;

    public $order_id;
    public ?string $admin_note = null;
    public ?string $cancel_reason = null;

    public function rules(){
        return $this->baseRequestRules(isAdmin: true);
    }

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

    public function saveAdminNote(){
        if($this->order->allowAdminNote()){
            $this->validate([
                'admin_note' => 'nullable|string|max:500'
            ], [
                'admin_note.string' => 'The admin note must be a valid text.',
                'admin_note.max' => 'The admin note must not exceed 500 characters.',
            ]);

            $this->repository->update(
                idOrCriteria: $this->order_id,
                attributes: [
                    'admin_note' => $this->admin_note ?: null
                ]
            );

            $this->order->refresh();
            $this->dispatch('order-updated');
            session()->flash('admin-note-saved');
        }
    }

    #[On('order.cancelled')]
    public function cancelOrder(){
        if($this->order->allowCancel()){
            $this->validate([
                'cancel_reason' => 'nullable|string|max:255'
            ], [
                'cancel_reason.string' => 'The cancellation reason must be a valid text.',
                'cancel_reason.max' => 'The cancellation reason must not exceed 255 characters.',
            ]);

            $this->repository->update(
                idOrCriteria: $this->order_id,
                attributes: [
                    'status' => OrderStatus::ADMIN_CANCEL->value,
                    'cancelled_at' => now(),
                    'cancel_reason' => $this->cancel_reason ?: null
                ]
            );

            $this->order->refresh();
            $this->dispatch('order-updated');
            session()->flash('cancel-success');
        }
    }

    #[Title('Order Notes - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.orders.order-notes');
    }
}
