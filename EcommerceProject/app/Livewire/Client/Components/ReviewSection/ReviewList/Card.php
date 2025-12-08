<?php

namespace App\Livewire\Client\Components\ReviewSection\ReviewList;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $name,
        public int $score,
        public string $time,
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('client.components.review-section.review-list.card');
    }
}
