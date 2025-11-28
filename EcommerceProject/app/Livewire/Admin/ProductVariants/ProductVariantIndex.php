<?php

namespace App\Livewire\Admin\ProductVariants;

use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Throwable;

class ProductVariantIndex extends Component
{
    use WithPagination;

    public bool $isTrashed = false;
    protected ProductVariantRepositoryInterface $repository;

    public $product_id;
    public string $search = '';
    public ?int $status = null;
    public ?int $stock = null;

    public function boot(ProductVariantRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function mount(int $product){
        $this->product_id = $product;
    }

    public function resetFilters(){
        $this->reset('search', 'status');
        $this->resetPage();
    }

    #[On('variant.deleted')]
    public function softDelete(int $id){
        $this->repository->delete($id);
    }

    #[On('variant.restored')]
    public function restore(int $id){
        $this->repository->restore($id);
    }

    #[On('variant.forceDeleted')]
    public function forceDelete(int $id){
        try {
            $this->repository->forceDelete($id);
        }catch(Throwable $error) {
            Log::error("Failed to force delete product variant due to database constraint violation: {$error}");

            $this->dispatch('show-variant-error', [
                'title' => 'Cannot Delete Variant',
                'time' => now()->toISOString(),
                'message' => 'Cannot delete this variant because it is currently linked to existing orders or shopping carts.'
            ]);
        }
    }

    #[Title('Product Variant List - Bookio Admin')]
    #[Layout('layouts.admin')]
    public function render()
    {
        $variants = $this->repository->getAll(criteria: function(&$query) {
            $query->with('inventory');
            if($this->isTrashed) $query->onlyTrashed();

            $query->when($this->search, function($innerQuery){
                $innerQuery->whereLike('name', '%'. trim($this->search) .'%');
            })
            ->when(
                $this->status !== null,
                fn($innerQuery) => $innerQuery->where('status', $this->status)
            )->when(
                $this->stock !== null,
                function($innerQuery){
                    $hasStock = fn($subQuery) => $subQuery->where('stock', '>', 0);

                    $this->stock == 1
                        ? $innerQuery->whereHas('inventory', $hasStock)
                        : $innerQuery->whereDoesntHave('inventory', $hasStock);
                }
            )->where('product_id', $this->product_id);

            $query->latest();
        }, perPage: 7, columns: ['*'], pageName: 'VariantsPage');

        return view('admin.pages.product-variants.product-variant-index', compact('variants'));
    }
}
