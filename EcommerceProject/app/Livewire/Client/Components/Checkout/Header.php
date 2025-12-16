<?php

namespace App\Livewire\Client\Components\Checkout;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Header extends Component
{
    const STEP_PREVIEW = 'preview';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $icon,
        public string $title,
        public string $activeStep = ''
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('client.components.checkout.header');
    }
}
