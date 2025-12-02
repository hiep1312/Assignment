<?php

namespace App\Livewire\Client\Components\ProductGrid;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public float $avgRating,
        public int $totalReviews,
        public int $stockQuantity,
        public int $soldCount,
        public int $price,
        public ?int $originalPrice = null,
        public float $discountPercent = 0,
        public bool $isNew = false,
        public bool $isPlaceholder = false
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('client.components.product-grid.card');
    }
}
