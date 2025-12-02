<?php

namespace App\Livewire\Client\Components\ProductGrid;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CardPlaceholder extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('client.components.product-grid.card-placeholder');
    }
}
