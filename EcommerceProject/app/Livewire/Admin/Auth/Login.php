<?php

namespace App\Livewire\Admin\Auth;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

class Login extends Component
{
    use AutoValidatesRequest;

    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    protected string $request = LoginRequest::class;

    public function mount(){
        if(Auth::check()) return redirect()->route('admin.dashboard');
    }

    public function handleLogin(){
        $this->validate();

        if(Auth::attempt([
            fn($query) => $query->where('email', $this->username)->orWhere('username', $this->username),
            'password' => $this->password
        ], $this->remember)){
            request()->session()->regenerate();
            return redirect()->intended('admin/dashboard');
        };

        $this->addError('auth', 'Invalid login credentials.');
    }

    #[Title('Login - Bookio Admin')]
    public function render()
    {
        return view('admin.pages.auth.login')
            ->extends('layouts.admin')
            ->section('body');
    }
}
