<?php

namespace App\Livewire\Client\Auth;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    #[Title('Sign In - Bookio')]
    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.auth.login');
    }
}
