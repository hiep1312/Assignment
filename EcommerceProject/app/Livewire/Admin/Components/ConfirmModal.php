<?php

namespace App\Livewire\Admin\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class ConfirmModal extends Component
{
    public string $title = '';
    public string $type = 'question';
    public string $message = '';
    public string $id = 'confirm-modal';
    public string $confirmLabel = 'Confirm';
    public string $eventName = 'modal.confirmed';
    public array $eventData = [];
    public bool $realtimeOpen = false;

    #[On('modal.show')]
    public function setup(
        $title = '',
        $type = 'question',
        $message = '',
        $id = 'confirm-modal',
        $confirmLabel = 'Confirm',
        $eventName = 'modal.confirmed',
        $eventData = [],
        $realtimeOpen = false
    ){
        $this->fill(compact('title', 'type', 'message', 'id', 'confirmLabel', 'eventName', 'eventData', 'realtimeOpen'));
        $this->js("syncModal", $realtimeOpen);
    }

    public function confirmAction()
    {
        $this->dispatch($this->eventName, ...$this->eventData);
        $this->js("syncModal", false);
    }

    public function render()
    {
        return view('admin.components.confirm-modal');
    }
}
