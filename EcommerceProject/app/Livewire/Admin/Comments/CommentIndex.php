<?php

namespace App\Livewire\Admin\Comments;

use App\Repositories\Contracts\BlogCommentRepositoryInterface;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

class CommentIndex extends Component
{
    use WithPagination;

    public bool $isTrashed = false;
    protected BlogCommentRepositoryInterface $repository;
    protected BlogRepositoryInterface $blogRepository;

    public string $search = '';
    public string $commentType = '';
    public ?int $blogId = null;
    public array $selectedRecordIds = [];
    public ?int $selectedCommentId = null;

    public function boot(BlogCommentRepositoryInterface $repository, BlogRepositoryInterface $blogRepository){
        $this->repository = $repository;
        $this->blogRepository = $blogRepository;
    }

    public function updatedIsTrashed(){
        $this->reset('selectedRecordIds');
        $this->js(<<<JS
            new Promise(resolve => setTimeout(updateSelectAllState));
        JS);
    }

    public function resetFilters(){
        $this->reset('search', 'blogId');
        $this->resetPage();
    }

    #[On('comment.deleted')]
    public function softDelete(?int $id = null){
        $this->repository->delete($id ?? fn(&$query) => $query->whereIn('id', $this->selectedRecordIds));
    }

    #[On('comment.restored')]
    public function restore(?int $id = null){
        $this->repository->restore(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[On('comment.forceDeleted')]
    public function forceDelete(?int $id = null){
        $this->repository->forceDelete(
            $id ??
            (empty($this->selectedRecordIds) ? null : fn(&$query) => $query->whereIn('id', $this->selectedRecordIds))
        );
    }

    #[Title('Comment List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $blogs = $this->blogRepository->getAll(criteria: function(&$query) {
            $userFilter = function ($userQuery) {
                $userQuery->when(
                    $this->search,
                    fn($innerQuerySearch) => $innerQuerySearch->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%'. trim($this->search) .'%'])
                );
            };

            $commentFilter = function ($commentQuery, bool $withUserRelation = false) use ($userFilter) {
                $withUserRelation && $commentQuery->with('user');

                $commentQuery->when(
                        $this->isTrashed,
                        fn($innerQuery) => $innerQuery->onlyTrashed()
                    )->when(
                        $this->search,
                        fn($innerQuerySearch) => $innerQuerySearch->where(function($innerQuerySearchGroup) use ($userFilter) {
                            $innerQuerySearchGroup->whereLike('content', '%'. trim($this->search) .'%')
                                ->orWhereHas('user', $userFilter);
                        })
                    )->when(
                        $this->commentType,
                        fn($innerQuery) => $this->commentType === "parent"
                            ? $innerQuery->whereNull('parent_id')
                            : $innerQuery->whereNotNull('reply_to')
                    )->whereHas('user');
            };

            $query->with(['author', 'thumbnail', 'comments' => fn($blogQuery) => $commentFilter($blogQuery, true)])
                ->withCount('comments');

            $query->whereHas('comments', $commentFilter)
                ->when(
                    $this->blogId !== null,
                    fn($innerQuery) => $innerQuery->where('id', $this->blogId)
                );

            $query->latest();
        }, perPage: 10, columns: ['id', 'title', 'description', 'status'], pageName: 'page');

        $selectedComment = null;
        if($this->selectedCommentId){
            $selectedComment = $this->repository->first(criteria: function(&$query) {
                $query->with('parent.user', 'replyTo.user', 'user');
                $query->where('id', $this->selectedCommentId);
            });
        }

        return view('admin.pages.comments.comment-index', compact('blogs', 'selectedComment'));
    }
}
