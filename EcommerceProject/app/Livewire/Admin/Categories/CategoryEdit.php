<?php

namespace App\Livewire\Admin\Categories;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\CategoryRequest;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;

class CategoryEdit extends Component
{
    use AutoValidatesRequest {
        rules as baseRequestRules;
    }

    public $id;
    public $name = '';
    public $slug = '';

    protected CategoryRepositoryInterface $repository;
    protected $request = CategoryRequest::class;

    public function rules(){
        return $this->baseRequestRules(isEdit: true, recordId: $this->id);
    }

    public function boot(CategoryRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function mount(int $category){
        $category = $this->repository->find(idOrCriteria: $category, throwNotFound: true);

        $this->fill($category->only([
            'id',
            'name',
            'slug'
        ]));
    }

    public function updatedName(){
        $this->slug = Str::slug($this->name);
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            $this->id,
            $this->only(['name', 'slug'])
        );

        return redirect()->route('admin.categories.index')->with('data-changed', ['Category has been updated successfully.', now()->toISOString()]);
    }

    public function resetForm(){
        $this->reset('name', 'slug');
        $this->mount($this->id);
    }

    #[Title('Edit Category - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.categories.category-edit');
    }
}
