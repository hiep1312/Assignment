<?php

namespace App\Livewire\Client\Checkout;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class OrderPreview extends Component
{


    #[Title('Order Preview - Bookio')]
    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.checkout.order-preview');
    }
}
