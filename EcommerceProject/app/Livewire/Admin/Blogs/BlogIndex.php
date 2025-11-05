<?php

namespace App\Livewire\Admin\Blogs;

use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class BlogIndex extends Component
{
    use WithPagination;

    public bool $isTrashed = false;
    protected BlogRepositoryInterface $repository;
    protected UserRepositoryInterface $userRepository;

    public string $search = '';
    public ?int $status = null;
    public ?int $authorId = null;
    public array $selectedRecordIds = [];
    public ?int $selectedBlogId = null;

    public function boot(BlogRepositoryInterface $repository, UserRepositoryInterface $userRepository){
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
        $this->reset('search', 'status', 'authorId');
        $this->resetPage();
    }

    #[On('blog.deleted')]
    public function softDelete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[On('blog.restored')]
    public function restore(?int $id = null){
        $this->repository->restore(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[On('blog.forceDeleted')]
    public function forceDelete(?int $id = null){
        $this->repository->forceDelete(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[Title('Blog List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $blogs = $this->repository->getAll(criteria: function(&$query) {
            if($this->isTrashed) $query->onlyTrashed();
            $query->with('author', 'categories', 'thumbnail')->withCount('comments');

            $query->when($this->search, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('title', '%'. trim($this->search) .'%')
                        ->orWhereLike('content', '%'. trim($this->search) .'%');
                });
            })
            ->when(
                $this->status !== null,
                fn($innerQuery) => $innerQuery->where('status', $this->status)
            )
            ->when(
                $this->authorId !== null,
                fn($innerQuery) => $innerQuery->where('author_id', $this->authorId)
            );

            $query->latest();
        }, perPage: 20, columns: ['*'], pageName: 'page');

        $authors = $this->userRepository->getAll(
            criteria: fn(&$query) => $query->whereHas('categories'),
            perPage: false,
            columns: ['id', 'first_name', 'last_name']
        );

        return view('admin.pages.blogs.blog-index', compact('blogs', 'authors'));
    }
}
