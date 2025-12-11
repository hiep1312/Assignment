<?php

namespace App\Livewire\Client\Cart;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CartIndex extends Component
{
    public bool $isGuest = false;
    public array $currentUser = [];


    #[Title('Shopping Cart - Bookio')]
    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.cart.cart-index');
    }
}
