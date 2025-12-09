<?php

namespace App\Livewire\Client\Partials;

use Livewire\Attributes\On;
use Livewire\Component;

class Error extends Component
{
    public ?int $status = null;

    #[On('error:show')]
    public function show(int|string $status)
    {
        $this->status = (int) $status;
        $this->js(<<<JS
            window.BasePageController._hidePageForError();
        JS);
    }

    public function render()
    {
        return view('client.partials.error');
    }
}
