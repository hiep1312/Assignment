<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ConfirmModal extends Component
{
    public static string $idModal;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public string $title,
        public string $type,
        public string $message = '',
        public string $id = 'confirm-modal',
        public string $confirmLabel = 'Confirm',
        public ?string $confirmAction = null
    ){
        self::$idModal = $id;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('admin.components.confirm-modal');
    }
}
