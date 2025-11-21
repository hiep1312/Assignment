<?php

namespace App\Livewire\Admin\Products;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ImageableRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use App\Repositories\Contracts\ProductVariantInventoryRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ProductIndex extends Component
{
    use WithPagination;

    public bool $isTrashed = false;
    protected ProductRepositoryInterface $repository;
    protected ProductVariantInventoryRepositoryInterface $variantInventoryRepository;
    protected ImageableRepositoryInterface $imageableRepository;
    protected ProductReviewRepositoryInterface $reviewRepository;
    protected CategoryRepositoryInterface $categoryRepository;

    public string $search = '';
    public ?int $status = null;
    public ?int $categoryId = null;
    public array $selectedRecordIds = [];
    public ?int $recordDetail = null;

    public function boot(ProductRepositoryInterface $repository, ProductVariantInventoryRepositoryInterface $variantInventoryRepository, ImageableRepositoryInterface $imageableRepository, ProductReviewRepositoryInterface $reviewRepository, CategoryRepositoryInterface $categoryRepository){
        $this->repository = $repository;
        $this->variantInventoryRepository = $variantInventoryRepository;
        $this->imageableRepository = $imageableRepository;
        $this->reviewRepository = $reviewRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function updatedIsTrashed(){
        $this->reset('selectedRecordIds');
        $this->js(<<<JS
            new Promise(resolve => setTimeout(updateSelectAllState));
        JS);
    }

    public function resetFilters(){
        $this->reset('search', 'role', 'emailVerified');
        $this->resetPage();
    }

    #[On('product.deleted')]
    public function softDelete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[On('product.restored')]
    public function restore(?int $id = null){
        $this->repository->restore(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[On('product.forceDeleted')]
    public function forceDelete(?int $id = null){
        $this->repository->forceDelete(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds)),
            function ($products) {
                if($products instanceof Collection){
                    $this->imageableRepository->delete(function(&$query) use ($products){
                        $query->whereIn('imageable_id', $products->pluck('id'))
                            ->where('imageable_type', $this->repository->getModel());
                    });
                }else {
                    $products->images()->detach();
                }
            }
        );
    }

    #[Title('Product List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $products = $this->repository->getAll(criteria: function(&$query) {
            $query->with(['mainImage', 'images' => function($subQuery){
                $subQuery->where('imageables.is_main', false);
            }, 'variants.inventory'])->withCount('variants');
            if($this->isTrashed) $query->onlyTrashed();

            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('title', '%'. trim($this->search) .'%')
                        ->orWhereLike('description', '%'. trim($this->search) .'%');
                });
            })
            ->when(
                $this->status !== null,
                fn($innerQuery) => $innerQuery->where('status', $this->status)
            )->when(
                $this->categoryId !== null,
                fn($innerQuery) => $innerQuery->whereHas('categories', function($subQuery) {
                    $subQuery->where('categories.id', $this->categoryId);
                })
            );

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        $categories = $this->categoryRepository->getAll(criteria: function(&$query){
            $query->whereHas('products');
        }, perPage: false, columns: ['id', 'name']);

        $statistic = [
            [
                'title' => 'Total Products',
                'value' => $this->repository->count(['withTrashed']),
                'icon' => 'fas fa-box',
            ],
            [
                'title' => 'Active Products',
                'value' => $this->repository->count(fn($query) => $query->where('status', 1)),
                'icon' => 'fas fa-check-circle',
            ],
            [
                'title' => 'Stockout Variants',
                'value' => $this->variantInventoryRepository->count(fn($query) => $query->where('stock', '<=', 0)),
                'icon' => 'fas fa-exclamation-triangle',
            ],
            [
                'title' => 'Average Rating',
                'value' => number_format($this->reviewRepository->avg('rating'), 1, '.', ''),
                'icon' => 'fas fa-star',
            ]
        ];

        return view('admin.pages.products.product-index', compact('products', 'categories', 'statistic'));
    }
}
