<?php

namespace App\Livewire\Admin\Banners;

use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Repositories\Contracts\ImageableRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class BannerIndex extends Component
{
    use WithPagination;

    protected BannerRepositoryInterface $repository;
    protected ImageableRepositoryInterface $imageableRepository;

    public string $search = '';
    public ?int $status = null;
    public string $sortOrder = '';
    public array $selectedRecordIds = [];

    public function boot(BannerRepositoryInterface $repository, ImageableRepositoryInterface $imageableRepository){
        $this->repository = $repository;
        $this->imageableRepository = $imageableRepository;
    }

    public function resetFilters(){
        $this->reset('search', 'status', 'sortOrder');
        $this->resetPage();
    }

    #[On('banner.deleted')]
    public function delete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds), function($banners){
            if($banners instanceof Collection){
                $this->imageableRepository->delete(function(&$query) use ($banners){
                    $query->whereIn('imageable_id', $banners->pluck('id'))
                        ->where('imageable_type', $this->repository->getModel());
                });
            }else{
                $banners->imageable()->delete();
            }
        });
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
            $query->with('image');

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
