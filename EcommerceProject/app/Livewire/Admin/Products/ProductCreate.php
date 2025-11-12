<?php

namespace App\Livewire\Admin\Products;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\ProductRequest;
use App\Http\Requests\Admin\ProductVariantRequest;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductVariantInventoryRepositoryInterface;
use Illuminate\Support\Arr;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class ProductCreate extends Component
{
    use AutoValidatesRequest;

    public $title = '';
    public $slug = '';
    public $description = null;
    public $status = 1;
    public $category_ids = [];
    public $image_ids = [];
    public $mainImageId = null;
    public $variants = [];

    public string $searchCategories = '';
    public array $selectedCategoryIds = [];
    public array $activeVariantData = [];

    protected ProductRepositoryInterface $repository;
    protected ProductVariantInventoryRepositoryInterface $variantInventoryRepository;
    protected ImageRepositoryInterface $imageRepository;
    protected CategoryRepositoryInterface $categoryRepository;
    protected $request = ProductRequest::class;

    public function boot(ProductRepositoryInterface $repository, ProductVariantInventoryRepositoryInterface $variantInventoryRepository, ImageRepositoryInterface $imageRepository, CategoryRepositoryInterface $categoryRepository){
        $this->repository = $repository;
        $this->variantInventoryRepository = $variantInventoryRepository;
        $this->imageRepository = $imageRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function updatedTitle(){
        $this->slug = Str::slug($this->title);
    }

    public function updateMainImage(){
        if(is_null($this->mainImageId) || !in_array($this->mainImageId, $this->image_ids)){
            $this->mainImageId = reset($this->image_ids);
        }
    }

    public function store(){
        $this->validate();

        $productCreated = $this->repository->create($this->only([
            'title',
            'slug',
            'description',
            'status'
        ]));

        $productCreated->categories()->attach($this->category_ids);

        $pivotImageData = collect($this->image_ids)
            ->mapWithKeys(fn($id, $position) => [
                $id => ['is_main' => $id == $this->mainImageId, 'position' => $position + 1]
            ])->toArray();

        $productCreated->images()->attach($pivotImageData);

        $variantsData = array_values($this->variants);
        $variantsAttributes = array_map(fn($variant) => Arr::except($variant, ['stock']), $variantsData);
        $variantsStock = array_column($variantsData, 'stock');

        $variantsCreated = $productCreated->variants()->createMany($variantsAttributes);
        $inventoriesData = [];
        foreach($variantsCreated as $index => $variant){
            $inventoriesData[] = [
                'variant_id' => $variant->id,
                'stock' => $variantsStock[$index]
            ];
        }

        $this->variantInventoryRepository->upsert($inventoriesData, 'variant_id', ['stock']);

        return redirect()->route('admin.products.index')->with('data-changed', ['New product has been created successfully.', now()->toISOString()]);
    }

    public function selectCategories(){
        $this->category_ids = $this->selectedCategoryIds;
        $this->reset('searchCategories', 'selectedCategoryIds');
    }

    #[On('images.attached')]
    public function onImageSelected(array $imageIds){
        $this->image_ids = array_unique([...$this->image_ids, ...$imageIds], SORT_NUMERIC);
        $this->updateMainImage();
    }

    public function resetForm(){
        $this->reset('title', 'slug', 'description', 'status', 'category_ids', 'image_ids', 'mainImageId', 'variants');
    }

    public function removeImage(int $imageId){
        $this->image_ids = array_filter($this->image_ids, fn($id) => $id != $imageId);
        $this->updateMainImage();
    }

    public function addVariant(){
        $this->activeVariantData = [
            'name' => '',
            'sku' => '',
            'price' => null,
            'discount' => null,
            'status' => 1,
            'stock' => null
        ];
    }

    public function editVariant(string $keyId){
        $this->activeVariantData = $this->variants[$keyId];
        $this->activeVariantData['keyId'] = $keyId;
    }

    #[On('variant.removed')]
    public function removeVariant(string $keyId){
        unset($this->variants[$keyId]);
    }

    public function handleVariantModal(bool $isEditing){
        $requestValidate= new ProductVariantRequest("product");
        $this->validate($requestValidate->rules(), $requestValidate->messages());

        if($isEditing && isset($this->activeVariantData['keyId'])){
            $keyId = $this->activeVariantData['keyId'];
            unset($this->activeVariantData['keyId']);

            $this->variants[$keyId] = $this->activeVariantData;
        }else{
            $this->variants[uniqid("variant")] = $this->activeVariantData;
        }

        $this->reset('activeVariantData');
        $this->js(<<<JS
            bootstrap.Modal.getOrCreateInstance("#variantModal").hide();
        JS);
    }

    #[Title('Add New Product - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $categories = $this->categoryRepository->getAll(criteria: function(&$query){
            $query->when($this->searchCategories, function($innerQuery){
                $innerQuery->whereLike('name', '%'. trim($this->searchCategories) .'%');
            });
        }, perPage: false, columns: ['id', 'name']);

        $category_names = $categories->whereIn('id', $this->category_ids)
            ->pluck('name')
            ->toArray();

        $images = $this->imageRepository->find(idOrCriteria: function(&$query){
            $query->whereIn('id', $this->image_ids)
                ->when($this->image_ids, fn($query) => $query->orderByRaw('FIELD(id, '. implode(',', $this->image_ids) .')'));
        }, columns: ['id', 'image_url']);

        return view('admin.pages.products.product-create', compact('categories', 'category_names', 'images'));
    }
}
