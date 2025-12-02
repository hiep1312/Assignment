<?php

namespace App\Livewire\Client\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Pagination extends Component
{
    public bool $isPlaceholder = false;

    /**
     * Create a new component instance.
     */
    public function __construct(
        public array $pagination
    ){
        $requiredKeys = [
            'current_page', 'last_page', 'per_page',
            'first_page_url', 'last_page_url', 'links',
            'next_page_url', 'prev_page_url', 'path',
            'from', 'to', 'total'
        ];

        $missingKeys = array_diff(
            $requiredKeys,
            array_keys($pagination)
        );

        if(!(empty($missingKeys) && is_array($pagination['links'] ?? null))) {
            $this->isPlaceholder = true;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('client.components.pagination');
    }
}
