<?php

namespace App\Livewire\Client\Products;

use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductShow extends Component
{
    public bool $isDataLoading = true;
    public string $routeSlug;
    public array $currentProduct = [];

    public function mount(string $product)
    {
        $this->routeSlug = $product;
    }

    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.products.product-show')
            ->title($this->routeSlug . " - Bookio");
    }
}
