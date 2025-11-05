<?php

namespace App\Livewire\Admin\Products;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductVariantRequest;
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

class ProductEdit extends Component
{
    use AutoValidatesRequest {
        rules as baseRequestRules;
    }

    public $id;
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

    public function rules(){
        return $this->baseRequestRules(isEdit: true, recordId: $this->id);
    }

    public function boot(ProductRepositoryInterface $repository, ProductVariantInventoryRepositoryInterface $variantInventoryRepository, ImageRepositoryInterface $imageRepository, CategoryRepositoryInterface $categoryRepository){
        $this->repository = $repository;
        $this->variantInventoryRepository = $variantInventoryRepository;
        $this->imageRepository = $imageRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function mount(int $product){
        $product = $this->repository->first(criteria: function(&$query) use ($product){
            $query->with(['categories', 'images' => function($subQuery){
                $subQuery->orderBy('imageables.position');
            }, 'mainImages', 'variants.inventory']);

            $query->where('id', $product);
        }, throwNotFound: true);

        $this->fill($product->only([
                'id',
                'title',
                'slug',
                'description',
                'status'
            ]) + [
                'category_ids' => $product->categories->pluck('id')->toArray(),
                'image_ids' => $product->images->pluck('id')->toArray(),
                'mainImageId' => $product->getMainImageAttribute()?->id,
                'variants' => $product->variants->keyBy('id')->map(function($variant){
                    return $variant->only(['id', 'name', 'sku', 'price', 'discount', 'status']) + [
                        'stock' => $variant->inventory?->stock
                    ];
                })->toArray()
            ]
        );
    }

    public function updatedTitle(){
        $this->slug = Str::slug($this->title);
    }

    public function updateMainImage(){
        if(is_null($this->mainImageId) || !in_array($this->mainImageId, $this->image_ids)){
            $this->mainImageId = reset($this->image_ids);
        }
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            $this->id,
            $this->only([
                'title',
                'slug',
                'description',
                'status'
            ]),
            $productUpdated
        );

        $productUpdated->categories()->sync($this->category_ids);

        $pivotImageData = collect($this->image_ids)
            ->mapWithKeys(fn($id, $position) => [
                $id => ['is_main' => $id == $this->mainImageId, 'position' => $position + 1]
            ])->toArray();

        $productUpdated->images()->sync($pivotImageData);

        /* Handle variants */
        $variantCurrentIds = $productUpdated->variants()->pluck('id')->toArray();
        $variantNewIds = array_column($this->variants, 'id');
        $variantIdsToDelete = array_diff($variantCurrentIds, $variantNewIds);

        if(!empty($variantIdsToDelete)){
            $productUpdated->variants()->whereIn('id', $variantIdsToDelete)->delete();
        }

        $variantsData = [];
        $inventoriesData = [];

        foreach($this->variants as $variant){
            $variantsData[] = Arr::except($variant, 'stock');

            $inventoriesData[$variant['sku']] = [
                'stock' => $variant['stock']
            ];
        }

        $productUpdated->variants()->upsert($variantsData, 'id', array_keys(Arr::except($variantsData[0], 'id')));
        $variantsCreated = $productUpdated->variants()->whereIn('sku', array_keys($inventoriesData))->get(['id', 'sku']);

        foreach($variantsCreated as $variant){
            $inventoriesData[$variant->sku]['variant_id'] = $variant->id;
        }

        $this->variantInventoryRepository->upsert($inventoriesData, 'variant_id', ['stock']);

        return redirect()->route('admin.products.index')->with('data-changed', ['Product has been updated successfully.', now()->toISOString()]);
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
        $this->mount($this->id);
    }

    public function removeImage(int $imageId){
        $this->image_ids = array_filter($this->image_ids, fn($id) => $id != $imageId);
        $this->updateMainImage();
    }

    public function addVariant(){
        $this->activeVariantData = [
            'id' => null,
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
        $this->validate(
            $requestValidate->rules(isset($this->activeVariantData['id']), $this->activeVariantData['id'] ?? null),
            $requestValidate->messages()
        );

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

    #[Title('Edit Product - Bookio Admin')]
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

        $images = $this->imageRepository->find(idOrCriteria: function(&$query){
            $query->whereIn('id', $this->image_ids)
                ->when($this->image_ids, fn($query) => $query->orderByRaw('FIELD(id, '. implode(',', $this->image_ids) .')'));
        }, columns: ['id', 'image_url']);

        return view('admin.pages.products.product-edit', compact('categories', 'category_names', 'images'));
    }
}
