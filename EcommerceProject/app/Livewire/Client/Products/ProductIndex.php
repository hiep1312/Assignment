<?php

namespace App\Livewire\Client\Products;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductIndex extends Component
{
    public array $categories = [];
    public array $ratingStatistics = [];
    public array $products = [];
    public array $pagination = [];
    public bool $isCardLoading = true;
    public array $priceRange = [];

    #[Title('Explore Books - Bookio')]
    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.products.product-index');
    }
}
