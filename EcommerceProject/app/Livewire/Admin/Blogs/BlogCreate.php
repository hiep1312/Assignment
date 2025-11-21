<?php

namespace App\Livewire\Admin\Blogs;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\BlogRequest;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;

class BlogCreate extends Component
{
    use AutoValidatesRequest;

    public $title = '';
    public $slug = '';
    public $content = '';
    public $status = 0;
    public $thumbnail_id = null;
    public $category_ids = [];

    public string $searchCategories = '';
    public array $selectedCategoryIds = [];
    public array $activeVariantData = [];

    protected BlogRepositoryInterface $repository;
    protected ImageRepositoryInterface $imageRepository;
    protected CategoryRepositoryInterface $categoryRepository;
    protected $request = BlogRequest::class;

    public function boot(BlogRepositoryInterface $repository, ImageRepositoryInterface $imageRepository, CategoryRepositoryInterface $categoryRepository){
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function updatedTitle(){
        $this->slug = Str::slug($this->title);
    }

    public function store(){
        $this->validate();

        $blogCreated = $this->repository->create(
            $this->only([
                'title',
                'slug',
                'content',
                'status'
            ]) + [
                'author_id' => Auth::id(),
            ]
        );

        $blogCreated->imageable()->create(['image_id' => $this->thumbnail_id, 'is_main' => true]);
        $blogCreated->categories()->attach($this->category_ids);

        return redirect()->route('admin.blogs.index')->with('data-changed', ['New blog has been created successfully.', now()->toISOString()]);
    }

    #[On('images.attached')]
    public function onImageSelected(array $imageIds, mixed $isThumbnail){
        if($isThumbnail){
            $this->thumbnail_id = $imageIds[0];
        }else{
            $imageUrl = $this->imageRepository->find(idOrCriteria: function($query) use ($imageIds){
                $query->whereIn('id', $imageIds);
            }, columns: ['image_url'])->map(fn($image) => asset("storage/{$image->image_url}"))->toArray();

            if($imageUrl) $this->js("window.editorAPI.insertImage", $imageUrl);
        }
    }

    public function selectCategories(){
        $this->category_ids = $this->selectedCategoryIds;
        $this->reset('searchCategories', 'selectedCategoryIds');
    }

    public function resetForm(){
        $this->reset('title', 'slug', 'content', 'status', 'thumbnail_id', 'category_ids');
    }

    #[Title('Add New Blog - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = $this->categoryRepository->getAll(criteria: function(&$query){
            $query->when($this->searchCategories, function($innerQuery){
                $innerQuery->where(function($subQuery){
                    $subQuery->whereLike('name', '%'. trim($this->searchCategories) .'%');
                });
            });
        }, perPage: false, columns: ['id', 'name']);

        $category_names = $categories->whereIn('id', $this->category_ids)
            ->pluck('name')
            ->toArray();

        $thumbnail = is_int($this->thumbnail_id) ? $this->imageRepository->find($this->thumbnail_id) : null;

        return view('admin.pages.blogs.blog-create', compact('thumbnail', 'categories', 'category_names'));
    }
}
