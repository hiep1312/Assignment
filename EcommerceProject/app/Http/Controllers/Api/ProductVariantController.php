<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProductVariantRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductVariantInventoryRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductVariantController extends Controller
{
    const API_FIELDS = ['id', 'product_id', 'name', 'sku', 'price', 'discount', 'status', 'created_at'];
    const INVENTORY_FIELDS = ['variant_id', 'stock', 'reserved', 'sold_number'];

    public function __construct(
        protected ProductVariantRepositoryInterface $repository,
        protected ProductVariantInventoryRepositoryInterface $inventoryRepository,
        protected ProductRepositoryInterface $productRepository
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slugProduct)
    {
        $variants = $this->repository->getAll(
            criteria: function(&$query) use ($request, $slugProduct) {
                $query->with('inventory:' . implode(',', self::INVENTORY_FIELDS));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->where(function($subQuery) use ($request){
                        $subQuery->whereLike('name', '%'. trim($request->search) .'%')
                            ->orWhereLike('sku', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->status),
                    fn($innerQuery) => $innerQuery->where('status', $request->status)
                )->when(
                    isset($request->price_range),
                    function($innerQuery) use ($request){
                        [$minPrice, $maxPrice] = is_array($request->price_range) ? $request->price_range : preg_split('/\s*-\s*/', $request->price_range);

                        $innerQuery->whereRaw('COALESCE(discount, price) BETWEEN ? AND ?', [$minPrice, $maxPrice]);
                    }
                );

                $query->whereHas('product', function($subQuery) use ($request, $slugProduct){
                    $subQuery->where('slug', $slugProduct ?? $request->product);
                });
            },
            perPage: min($request->integer('per_page', 20), 50),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return response()->json([
            'success' => true,
            'message' => 'Product variant list retrieved successfully.',
            ...$variants->toArray()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductVariantRequest $request, string $slugProduct)
    {
        if(!($product = $this->productRepository->first(fn($query) => $query->where('slug', $slugProduct)))){
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $validatedData = $request->validated();
        $variant = $product->variants()->create($validatedData);
        $inventory = $variant->inventory()->create(['stock' => $validatedData['stock']]);

        return response()->json([
            'success' => true,
            'message' => 'Product variant created successfully.',
            'data' => array_merge($variant->only(self::API_FIELDS), ['inventory' => $inventory->only(self::INVENTORY_FIELDS)]),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $skuVariant)
    {
        $variant = $this->repository->first(
            criteria: fn($query) => $query->where('sku', $skuVariant),
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return response()->json([
            'success' => (bool) $variant,
            'message' => $variant ? 'Product variant retrieved successfully.' : 'Product variant not found.',
            'data' => $variant?->only(self::API_FIELDS),
        ], $variant ? 200 : 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductVariantRequest $request, string $skuVariant)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: $request->id,
            attributes: $validatedData,
            updatedModel: $updatedProductVariant
        );

        $inventoryData = array_merge(
            Arr::only($request->inventory ?? [], self::INVENTORY_FIELDS),
            ['stock' => (int) $validatedData['stock']]
        );

        if($updatedProductVariant && $validatedData['stock'] !== $request->inventory['stock']){
            $updatedProductVariant->inventory()->update(['stock' => $validatedData['stock']]);
        }

        return response()->json([
            'success' => (bool) $isUpdated,
            'message' => $isUpdated
                ? 'Product variant updated successfully.'
                : 'Product variant not found.',
            'data' => $isUpdated ?
                array_merge($updatedProductVariant?->only(self::API_FIELDS), ['inventory' => $inventoryData])
                : null,
        ], $isUpdated ? 200 : 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $skuVariant)
    {
        $isDeleted = $this->repository->delete(
            idOrCriteria: fn($query) => $query->where('sku', $skuVariant)
        );

        return response()->json([
            'success' => (bool) $isDeleted,
            'message' => $isDeleted
                ? 'Product variant deleted successfully.'
                : 'Product variant not found.',
        ], $isDeleted ? 200 : 404);
    }
}
