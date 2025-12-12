<?php

namespace App\Livewire\Client\Products;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductIndex extends Component
{
    public array $categories = [];
    public array $ratingStatistics = [];
    public array $priceRange = [];

    public bool $isDataLoading = true;
    public array $products = [];
    public array $pagination = [];

    #[Title('Explore Books - Bookio')]
    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.products.product-index');
    }
}
