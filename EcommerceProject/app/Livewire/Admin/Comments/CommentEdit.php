<?php

namespace App\Livewire\Admin\Comments;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\BlogCommentRequest;
use App\Repositories\Contracts\BlogCommentRepositoryInterface;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class CommentEdit extends Component
{
    use AutoValidatesRequest {
        rules as baseRequestRules;
    }

    public $id;
    public $blog_id = null;
    public $content = '';
    public ?int $parent_id = null;
    public ?int $reply_to = null;

    protected BlogCommentRepositoryInterface $repository;
    protected BlogRepositoryInterface $blogRepository;
    protected $request = BlogCommentRequest::class;

    public function rules(){
        return $this->baseRequestRules(isEdit: true);
    }

    public function boot(BlogCommentRepositoryInterface $repository, BlogRepositoryInterface $blogRepository){
        $this->repository = $repository;
        $this->blogRepository = $blogRepository;
    }

    public function mount(int $comment){
        $comment = $this->repository->find(idOrCriteria: $comment, throwNotFound: true);

        $this->fill($comment->only([
            'id',
            'blog_id',
            'content',
            'parent_id',
            'reply_to'
        ]));
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            idOrCriteria: $this->id,
            attributes: $this->only([
                'content',
            ]),
        );

        return redirect()->route('admin.comments.index')->with('data-changed', ['Comment has been updated successfully.', now()->toISOString()]);
    }

    public function resetForm(){
        $this->reset('blog_id', 'content', 'parent_id', 'reply_to');
        $this->mount($this->id);
    }

    #[Title('Edit Comment - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $blog_title = $this->blogRepository->find(idOrCriteria: $this->blog_id, columns: ['title'])?->title;

        $parentCommentSelected = $this->repository->first(criteria: function(&$query){
            $query->with('user');

            $query->where('blog_id', $this->blog_id)
                ->where('id', $this->parent_id)
                ->whereHas('user');
        });

        $replyCommentSelected = $this->repository->first(criteria: function(&$query){
            $query->with('user');

            $query->where('blog_id', $this->blog_id)
                ->where('id', $this->reply_to)
                ->whereHas('user');
        });

        return view('admin.pages.comments.comment-edit', compact('blog_title', 'parentCommentSelected', 'replyCommentSelected'));
    }
}
