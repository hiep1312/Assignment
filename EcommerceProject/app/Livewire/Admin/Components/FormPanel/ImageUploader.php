<?php

namespace App\Livewire\Admin\Components\FormPanel;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageUploader extends Component
{
    const TYPE_AVATAR = 'avatar';
    const TYPE_BANNER = 'banner';
    const TYPE_PRODUCT = 'product';
    const TYPE_BLOG = 'blog';

    /**
     * Create a new component instance.
     */
    public function __construct(
        public bool $isMultiple,
        public string $type,
        public string $label,
        public string $labelIcon = 'fas fa-image',
    ){}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.form-panel.image-uploader');
    }
}
