<?php

namespace App\Livewire\Admin\Components\FormPanel;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Group extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title = '',
        public string $icon = '',
        public bool $hasTitleAction = false
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.form-panel.group');
    }
}
