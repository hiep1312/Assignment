<?php

namespace App\Livewire\Admin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ManagementHeader extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $btnLink = '',
        public string $btnLabel = '',
        public string $btnForInput = '',
        public string $btnIcon = 'fas fa-plus',
        public string $btnClass = 'btn btn-primary'
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.management-header');
    }
}
