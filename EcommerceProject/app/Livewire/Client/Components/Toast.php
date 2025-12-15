<?php

namespace App\Livewire\Client\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class Toast extends Component
{
    public string $toastId = 'notification-toast';
    public string $title = '';
    public string $message = '';
    public string $type = 'light';
    public int $duration = 12;
    public string $time = '';
    public string $animation = '';
    public string $icon = '';
    public bool $show = false;

    #[On('toast.show')]
    public function show(
        string $title,
        string $message,
        string $type,
        int $duration = 12,
        string $time = '',
        string $animation = '',
        string $icon = '',
        bool $show = true
    ){
        $this->fill(compact('title', 'message', 'type', 'duration', 'time', 'animation', 'icon', 'show'));
        $this->js("window.initLiveToast");
    }

    public function render()
    {
        return view('client.components.toast');
    }
}
