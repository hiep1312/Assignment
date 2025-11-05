<?php

namespace App\Livewire\Admin\Categories;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\CategoryRequest;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;

class CategoryCreate extends Component
{
    use AutoValidatesRequest;

    public $name = '';
    public $slug = '';

    protected CategoryRepositoryInterface $repository;
    protected $request = CategoryRequest::class;


    public function boot(CategoryRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function updatedName(){
        $this->slug = Str::slug($this->name);
    }

    public function store(){
        $this->validate();

        $this->repository->create(
            $this->only([
                'name', 'slug'
            ]) + [
                'created_by' => Auth::id()
            ]
        );

        return redirect()->route('admin.categories.index')->with('data-changed', ['New category has been created successfully.', now()->toISOString()]);
    }

    public function resetForm(){
        $this->reset('name', 'slug');
    }

    #[Title('Add New Category - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        return view('admin.pages.categories.category-create');
    }
}
