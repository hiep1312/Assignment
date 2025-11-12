<?php

namespace App\Livewire\Admin\Comments;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\BlogCommentRequest;
use App\Repositories\Contracts\BlogCommentRepositoryInterface;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CommentCreate extends Component
{
    use AutoValidatesRequest;

    public $blog_id = null;
    public $content = '';
    public ?int $parent_id = null;
    public ?int $reply_to = null;

    public string $searchBlogs = '';
    public ?int $selectedBlogId = null;

    protected BlogCommentRepositoryInterface $repository;
    protected BlogRepositoryInterface $blogRepository;
    protected $request = BlogCommentRequest::class;

    public function boot(BlogCommentRepositoryInterface $repository, BlogRepositoryInterface $blogRepository){
        $this->repository = $repository;
        $this->blogRepository = $blogRepository;
    }

    public function store(){
        $this->validate();

        $this->repository->create(
            $this->only([
                'blog_id',
                'content',
                'parent_id',
                'reply_to'
            ]) + [
                'user_id' => Auth::id(),
            ]
        );

        return redirect()->route('admin.comments.index')->with('data-changed', ['New comment has been created successfully.', now()->toISOString()]);
    }

    public function selectBlog(){
        $this->blog_id = $this->selectedBlogId;
        $this->reset('searchBlogs', 'selectedBlogId');
    }

    public function resetForm(){
        $this->reset('blog_id', 'content', 'parent_id', 'reply_to');
    }

    #[Title('Add New Comment - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $blogs = $this->blogRepository->getAll(criteria: function(&$query){
            $query->when($this->searchBlogs, function($innerQuery){
                $innerQuery->whereLike('title', '%'. trim($this->searchBlogs) .'%');
            });
        }, perPage: false, columns: ['id', 'title']);

        $parentComments = $this->repository->find(idOrCriteria: function(&$query){
            $query->with('user');

            $query->where('blog_id', $this->blog_id)
                ->whereNull('parent_id')
                ->whereHas('user');
        });

        $replyComments = $this->repository->find(idOrCriteria: function(&$query){
            $query->with('user');

            $query->where('blog_id', $this->blog_id)
                ->where('parent_id', $this->parent_id)
                ->whereHas('user');
        });

        $blog_title = $blogs->firstWhere('id', $this->blog_id)?->title;
        $parentCommentSelected = $parentComments->firstWhere('id', $this->parent_id);
        $replyCommentSelected = $replyComments->firstWhere('id', $this->reply_to);

        return view('admin.pages.comments.comment-create', compact('blogs', 'blog_title', 'parentComments', 'parentCommentSelected', 'replyComments', 'replyCommentSelected'));
    }
}
