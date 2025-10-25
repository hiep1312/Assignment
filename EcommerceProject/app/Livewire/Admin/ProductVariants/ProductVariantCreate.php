<?php

namespace App\Livewire\Admin\ProductVariants;

use App\Helpers\AutoValidatesRequest;
use App\Http\Requests\ProductVariantRequest;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

class ProductVariantCreate extends Component
{
    use AutoValidatesRequest;

    public $product_id;
    public $name = '';
    public $sku = '';
    public $price = null;
    public $discount = null;
    public $status = 1;
    public $stock = 0;

    protected ProductVariantRepositoryInterface $repository;
    protected $request = ProductVariantRequest::class;

    public string $searchVariants = '';
    public ?int $selectedVariantId = null;

    public function boot(ProductVariantRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function mount(int $product){
        if(!$this->repository->exists(criteria: fn(&$query) => $query->where('id', $product))) abort(404, 'Product not found.');

        $this->product_id = $product;
    }

    public function store(){
        $this->validate();

        $variantCreated = $this->repository->create($this->only([
            'product_id',
            'name',
            'sku',
            'price',
            'discount',
            'status'
        ]));

        $variantCreated->inventory()->create($this->only('stock'));

        return redirect()->route('admin.products.index')->with('data-changed', ['New variant has been created successfully.', now()->toISOString()]);
    }

    public function resetForm(){
        $this->reset('name', 'sku', 'price', 'discount', 'status', 'stock');
    }

    public function selectVariant(){
        if(is_int($this->selectedVariantId)){
            $this->name = $this->repository->find($this->selectedVariantId, ['name'])->name;
        }

        $this->reset('searchVariants', 'selectedVariantId');
    }

    #[Title('Add New Variant - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $availableVariants = $this->repository->getAll(criteria: function(&$query){
            $query->whereNot('product_id', $this->product_id)
                ->when($this->searchVariants, fn($innerQuery) => $innerQuery->whereLike('name', '%'. trim($this->searchVariants) .'%'))
                ->when($this->name, fn($innerQuery) => $innerQuery->whereNot('name', $this->name));
        }, perPage: false, columns: ['id', 'name']);

        return view('admin.pages.product-variants.product-variant-create', compact('availableVariants'));
    }
}
