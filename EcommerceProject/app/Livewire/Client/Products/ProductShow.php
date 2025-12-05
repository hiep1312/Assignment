<?php

namespace App\Livewire\Client\Products;

use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductShow extends Component
{

    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.products.product-show')
            ->title('Product Show');
    }
}
