<?php

namespace App\Livewire\Admin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataSelector extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $resetProperty = ''
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.data-selector');
    }
}
