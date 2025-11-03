<?php

namespace App\Livewire\Admin\Categories;

use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    public bool $isTrashed = false;
    protected CategoryRepositoryInterface $repository;
    protected UserRepositoryInterface $userRepository;

    public string $search = '';
    public ?int $createdBy = null;
    public string $categoryGroup = '';
    public array $selectedRecordIds = [];

    public function boot(CategoryRepositoryInterface $repository, UserRepositoryInterface $userRepository){
        $this->repository = $repository;
        $this->userRepository = $userRepository;
    }

    public function updatedIsTrashed(){
        $this->reset('selectedRecordIds');
        $this->js(<<<JS
            new Promise(resolve => setTimeout(updateSelectAllState));
        JS);
    }

    public function resetFilters(){
        $this->reset('search', 'createdBy', 'categoryGroup');
        $this->resetPage();
    }

    #[On('category.deleted')]
    public function softDelete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[On('category.restored')]
    public function restore(?int $id = null){
        $this->repository->restore(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[On('category.forceDeleted')]
    public function forceDelete(?int $id = null){
        $this->repository->forceDelete(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[Title('Category List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = $this->repository->getAll(criteria: function(&$query) {
            if($this->isTrashed) $query->onlyTrashed();
            $query->withCount('blogs', 'products');

            $query->when($this->search, function($innerQuery){
                $innerQuery->whereLike('name', '%'. trim($this->search) .'%');
            })
            ->when(
                $this->createdBy !== null,
                fn($innerQuery) => $innerQuery->where('created_by', $this->createdBy)
            )->when(
                $this->categoryGroup,
                fn($innerQuery) => $innerQuery->whereHas("{$this->categoryGroup}s")
            );

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        $creators = $this->userRepository->getAll(
            criteria: fn(&$query) => $query->whereHas('categories'),
            perPage: false,
            columns: ['id', 'first_name', 'last_name']
        );

        return view('admin.pages.categories.category-index', compact('categories', 'creators'));
    }
}
