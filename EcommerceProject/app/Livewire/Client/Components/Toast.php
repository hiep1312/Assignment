<?php

namespace App\Livewire\Client\Components;

use Livewire\Attributes\On;
use Livewire\Component;

class Toast extends Component
{
    public string $title;
    public string $message;
    public string $type;
    public int $duration = 12;
    public string $time = '';
    public string $animation = '';
    public string $icon = '';


    #[On('toast:show')]
    public function show(
        string $title,
        string $message,
        string $type,
        int $duration = 12,
        string $time = '',
        string $animation = '',
        string $icon = ''
    ){
        $this->fill(compact('title', 'message', 'type', 'duration', 'time', 'animation', 'icon'));
    }

    public function render()
    {
        return view('client.components.toast');
    }
}
