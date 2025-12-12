<?php

namespace App\Livewire\Client\Products;

use Livewire\Attributes\Layout;
use Livewire\Component;

class ProductShow extends Component
{
    public $routeSlug;
    public ?array $currentUser = null;

    public bool $isDataLoading = true;
    public array $currentProduct = [];
    public array $productCategories = [];
    public ?array $selectedVariant = [];

    public bool $isReviewsLoaded = false;
    public array $ratingDistribution = [];
    public array $reviewsData = [];
    public ?array $myReview = null;
    public array $reviewsPagination = [];
    public bool $isLoadingMore = false;
    public bool $canReview = false;

    public function mount(string $product)
    {
        $this->routeSlug = $product;
    }

    public function updatedCurrentProduct()
    {
        $this->selectedVariant = $this->currentProduct['variants'][0] ?? [
            'price' => 0,
            'inventory' => [
                'stock' => 0
            ]
        ];

        $reviewsApiUrl = route('api.products.reviews.index', $this->currentProduct['id'] ?? 0);
        $this->js(<<<JS
            window.reviewsApiUrl = "{$reviewsApiUrl}";
        JS);
    }

    public function updatedReviewsData()
    {
        $this->isLoadingMore = false;
    }

    #[Layout('layouts.client')]
    public function render()
    {
        return view('client.pages.products.product-show')
            ->title($this->routeSlug . " - Bookio");
    }
}
;
