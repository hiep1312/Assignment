<?php

namespace App\Livewire\Admin\Reviews;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewIndex extends Component
{
    use WithPagination;

    public bool $isTrashed = false;
    protected ProductReviewRepositoryInterface $repository;
    protected ProductRepositoryInterface $productRepository;

    public string $search = '';
    public ?int $rating = null;
    public ?int $productId = null;
    public array $selectedRecordIds = [];
    public ?int $selectedReviewId = null;

    public function boot(ProductReviewRepositoryInterface $repository, ProductRepositoryInterface $productRepository){
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function updatedIsTrashed(){
        $this->reset('selectedRecordIds');
        $this->js(<<<JS
            new Promise(resolve => setTimeout(updateSelectAllState));
        JS);
    }

    public function resetFilters(){
        $this->reset('search', 'rating', 'productId');
        $this->resetPage();
    }

    #[On('review.deleted')]
    public function softDelete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[On('review.restored')]
    public function restore(?int $id = null){
        $this->repository->restore(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[On('review.forceDeleted')]
    public function forceDelete(?int $id = null){
        $this->repository->forceDelete(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[Title('Review List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $products = $this->productRepository->getAll(criteria: function(&$query) {
            $userFilter = function ($userQuery) {
                $userQuery->when(
                    $this->search,
                    fn($innerQuerySearch) => $innerQuerySearch->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'. trim($this->search) .'%'])
                );
            };

            $reviewFilter = function ($reviewQuery, bool $withUserRelation = false) use ($userFilter) {
                $withUserRelation && $reviewQuery->with('user');

                $reviewQuery->when(
                        $this->isTrashed,
                        fn($innerQuery) => $innerQuery->onlyTrashed()
                    )->when(
                        $this->search,
                        fn($innerQuerySearch) => $innerQuerySearch->where(function($innerQuerySearchGroup) use ($userFilter) {
                            $innerQuerySearchGroup->whereLike('content', '%'. trim($this->search) .'%')
                                ->orWhereHas('user', $userFilter);
                        })
                    )->when(
                        $this->rating !== null,
                        fn($innerQuery) => $innerQuery->where('rating', $this->rating)
                    );
            };

            $query->with(['mainImages', 'reviews' => fn($reviewQuery) => $reviewFilter($reviewQuery, true)])
                ->withCount('reviews')
                ->withAvg('reviews', 'rating');

            $query->whereHas('reviews', $reviewFilter)
                ->when(
                    $this->productId !== null,
                    fn($innerQuery) => $innerQuery->where('id', $this->productId)
                );

            $query->latest();
        }, perPage: 10, columns: ['id', 'title', 'description', 'status'], pageName: 'page');

        $selectedReview = null;
        if($this->selectedReviewId){
            $selectedReview = $this->repository->find($this->selectedReviewId);
        }

        return view('admin.pages.reviews.review-index', compact('products', 'selectedReview'));
    }
}
