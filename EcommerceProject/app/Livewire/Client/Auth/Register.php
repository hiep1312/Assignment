<?php

namespace App\Livewire\Client\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Register extends Component
{

    #[Title('Sign Up - Bookio')]
    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.auth.register');
    }
}
