<?php

namespace App\Livewire\Admin\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FormPanel extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $isFormNormal = true,
        public string $id = 'form-panel',
        public string $action = '',
        public string $method = 'GET',
        public string $enctype = 'application/x-www-form-urlencoded'
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.form-panel');
    }
}
