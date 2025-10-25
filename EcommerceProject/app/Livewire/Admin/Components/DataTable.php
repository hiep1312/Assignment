<?php

namespace App\Livewire\Admin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $caption,
        public bool $isDetailFilter = false
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.data-table');
    }
}
