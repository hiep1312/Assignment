<?php

namespace App\Livewire\Admin\Blogs;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\BlogRequest;
use App\Models\Blog;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class BlogEdit extends Component
{
    use AutoValidatesRequest {
        rules as baseRequestRules;
    }

    public $id;
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

    public function rules(){
        return $this->baseRequestRules(isEdit: true, recordId: $this->id);
    }

    public function boot(BlogRepositoryInterface $repository, ImageRepositoryInterface $imageRepository, CategoryRepositoryInterface $categoryRepository){
        $this->repository = $repository;
        $this->imageRepository = $imageRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function mount(int $blog){
        $blog = $this->repository->first(criteria: function(&$query) use ($blog){
            $query->with(['thumbnail', 'categories']);

            $query->where('id', $blog);
        }, throwNotFound: true);

        $this->fill($blog->only([
                'id',
                'title',
                'slug',
                'content',
                'status'
            ]) + [
                'thumbnail_id' => $blog->thumbnail?->image_id,
                'category_ids' => $blog->categories->pluck('id')->toArray()
            ]
        );
    }

    public function updatedTitle(){
        $this->slug = Str::slug($this->title);
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            idOrCriteria: $this->id,
            attributes: $this->only([
                'title',
                'slug',
                'content',
                'status'
            ]) + [
                'author_id' => Auth::id(),
            ],
            updatedModel: $blogUpdated
        );

        $blogUpdated->categories()->sync($this->category_ids);
        $blogUpdated->imageable()->updateOrCreate([
            'imageable_id' => $blogUpdated->id,
            'imageable_type' => Blog::class,
            'is_main' => true
        ], [
            'image_id' => $this->thumbnail_id
        ]);

        return redirect()->route('admin.blogs.index')->with('data-changed', ['Blog has been updated successfully.', now()->toISOString()]);
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
        $this->mount($this->id);
    }

    #[Title('Edit Blog - Bookio Admin')]
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

        return view('admin.pages.blogs.blog-edit', compact('categories', 'category_names', 'thumbnail'));
    }
}
