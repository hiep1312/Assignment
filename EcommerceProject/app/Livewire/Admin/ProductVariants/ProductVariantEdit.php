<?php

namespace App\Livewire\Admin\ProductVariants;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\Admin\ProductVariantRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductVariantEdit extends Component
{
    use AutoValidatesRequest {
        rules as baseRequestRules;
    }

    public $id;
    public $product_id;
    public $name = '';
    public $sku = '';
    public $price = null;
    public $discount = null;
    public $status = 1;
    public $stock = 0;

    protected ProductVariantRepositoryInterface $repository;
    protected ProductRepositoryInterface $productRepository;
    protected $request = ProductVariantRequest::class;

    public string $searchVariants = '';
    public ?int $selectedVariantId = null;

    public function rules(){
        return $this->baseRequestRules(isEdit: true, recordId: $this->id);
    }

    public function boot(ProductVariantRepositoryInterface $repository, ProductRepositoryInterface $productRepository){
        $this->repository = $repository;
        $this->productRepository = $productRepository;
    }

    public function mount(int $product, int $variant){
        if(!$this->productRepository->exists(criteria: fn(&$query) => $query->where('id', $product))) abort(404, 'Product not found.');

        $this->product_id = $product;
        $variant = $this->repository->first(criteria: function(&$query) use ($variant){
            $query->with('inventory');

            $query->where('product_id', $this->product_id)
                ->where('id', $variant);
        }, throwNotFound: true);

        $this->fill(
            $variant->only([
                'id',
                'name',
                'sku',
                'price',
                'discount',
                'status'
            ]) + [
                'stock' => $variant->inventory->stock
            ]
        );
    }

    public function update(){
        $this->validate();

        $this->repository->update(
            idOrCriteria: $this->id,
            attributes: $this->only([
                'name',
                'sku',
                'price',
                'discount',
                'status'
            ]),
            updatedModel: $variantUpdated
        );

        $variantUpdated->inventory()->update($this->only('stock'));

        return redirect()->route('admin.products.index')->with('data-changed', ['Variant has been updated successfully.', now()->toISOString()]);
    }

    public function resetForm(){
        $this->reset('name', 'sku', 'price', 'discount', 'status', 'stock');
        $this->mount($this->product_id, $this->id);
    }

    public function selectVariant(){
        if(is_int($this->selectedVariantId)){
            $this->name = $this->repository->find($this->selectedVariantId, ['name'])->name;
        }

        $this->reset('searchVariants', 'selectedVariantId');
    }

    #[Title('Edit Variant - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $availableVariants = $this->repository->getAll(criteria: function(&$query){
            $query->whereNot('product_id', $this->product_id)
                ->when($this->searchVariants, fn($innerQuery) => $innerQuery->whereLike('name', '%'. trim($this->searchVariants) .'%'))
                ->when($this->name, fn($innerQuery) => $innerQuery->whereNot('name', $this->name));
        }, perPage: false, columns: ['id', 'name']);

        return view('admin.pages.product-variants.product-variant-edit', compact('availableVariants'));
    }
}
