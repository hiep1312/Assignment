<?php

namespace App\Livewire\Client\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReviewSection extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public float $avgRating = 0,
        public int $totalReviews = 0,
        public array $starCounts = [],
        public string $headerClass = '',
        public bool $isPlaceholder = false
    ){
        if(!empty($starCounts)) {
            $this->starCounts = array_column($starCounts, 'total', 'rating');
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('client.components.review-section');
    }
}
