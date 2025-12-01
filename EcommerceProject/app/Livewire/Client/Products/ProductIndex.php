<?php

namespace App\Livewire\Client\Products;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductIndex extends Component
{

    #[Title('Explore Books - Bookio')]
    #[Layout('layouts.client')]
    public function render()
    {
        $this->js('console.log', 'run');
        return view('client.pages.products.product-index');
    }
}
