<?php

namespace App\Livewire\Admin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FilterBar extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $placeholderSearch = 'Search data...',
        public string $modelSearch = 'search',
        public string $resetAction = 'resetFilters',
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.filter-bar');
    }
}
