<?php

namespace App\Livewire\Admin\Banners;

use App\Repositories\Contracts\BannerRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class BannerIndex extends Component
{
    use WithPagination;

    protected BannerRepositoryInterface $repository;

    public string $search = '';
    public ?int $status = null;
    public string $sortOrder = '';
    public array $selectedRecordIds = [];

    public function boot(BannerRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function resetFilters(){
        $this->reset('search', 'status', 'sortOrder');
        $this->resetPage();
    }

    #[On('banner.deleted')]
    public function delete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[On('banner.reordered')]
    public function reorder(){
        $this->repository->reorderPositions();
    }

    public function switchStatus(int $id){
        $this->repository->toggleStatusById($id);
        session()->flash('data-changed', ["Status of Banner #{$id} has been updated successfully.", now()->toISOString()]);
    }

    #[Title('Banner List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $banners = $this->repository->getAll(criteria: function(&$query) {
            $query->with('imageable.image');

            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('title', '%'. trim($this->search) .'%')
                        ->orWhereLike('link_url', '%'. trim($this->search) .'%');
                });
            })
            ->when(
                $this->status !== null,
                fn($innerQuery) => $innerQuery->where('status', $this->status)
            );

            if($this->sortOrder) {
                $this->repository->orderByImagePosition($query, $this->sortOrder);
            }else {
                $query->orderBy('id', 'desc');
            }
        }, perPage: 20, columns: ['*'], pageName: 'page');

        return view('admin.pages.banners.banner-index', compact('banners'));
    }
}
