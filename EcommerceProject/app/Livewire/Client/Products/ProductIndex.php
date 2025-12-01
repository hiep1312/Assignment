<?php

namespace App\Livewire\Client\Products;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductIndex extends Component
{
    public array $products = [];

    #[Title('Explore Books - Bookio')]
    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.products.product-index');
    }
}
