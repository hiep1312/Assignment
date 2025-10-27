<?php

namespace App\Livewire\Admin\Components\FormPanel\Group;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InputGroup extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $icon,
        public string $label = '',
        public string $column = 'col-md-6',
        public string $error = '',
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.form-panel.group.input-group');
    }
}
