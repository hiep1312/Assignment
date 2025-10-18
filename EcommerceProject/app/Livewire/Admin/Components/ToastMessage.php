<?php

namespace App\Livewire\Admin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ToastMessage extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $time,
        public string $type = 'white',
        public bool $show = false,
        public ?int $duration = 5
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.toast-message');
    }
}
