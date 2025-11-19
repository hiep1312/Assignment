<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\ProductVariantRequest;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Services\ProductVariantService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProductVariantController extends BaseApiController
{
    const API_FIELDS = ['id', 'product_id', 'name', 'sku', 'price', 'discount', 'status', 'created_at'];
    const INVENTORY_FIELDS = ['variant_id', 'stock', 'reserved', 'sold_number'];

    public function __construct(
        protected ProductVariantRepositoryInterface $repository,
        protected ProductVariantService $service
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
                        $priceRange = is_array($request->price_range) ? $request->price_range : preg_split('/\s*-\s*/', $request->price_range, 2);
                        $minPrice = is_numeric($priceRange[0]) ? (int) $priceRange[0] : 0;
                        $maxPrice = is_numeric($priceRange[1] ?? null) ? (int) $priceRange[1] : PHP_INT_MAX;

                        $innerQuery->whereRaw('COALESCE(discount, price) BETWEEN ? AND ?', [$minPrice, $maxPrice]);
                    }
                );

                $query->whereHas(
                    'product',
                    fn($subQuery) => $subQuery->where('slug', $slugProduct)
                );
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Product variant list retrieved successfully.',
            additionalData: $variants->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductVariantRequest $request, string $slugProduct)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        [$isCreated, $createdVariant, $createdInventory] = $this->service->create($validatedData, $slugProduct);

        return $this->response(
            success: (bool) $isCreated,
            message: $isCreated
                ? 'Product variant created successfully.'
                : 'Failed to create product variant.',
            code: $isCreated ? 201 : 400,
            data: array_merge(
                $createdVariant?->only(self::API_FIELDS) ?? [],
                $isCreated ? ['inventory' => $createdInventory->only(self::INVENTORY_FIELDS)] : []
            )
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $sku)
    {
        $variant = $this->repository->first(
            criteria: function($query) use ($sku){
                $query->with('inventory:' . implode(',', self::INVENTORY_FIELDS))
                    ->where('sku', $sku);
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $variant,
            message: $variant
                ? 'Product variant retrieved successfully.'
                : 'Product variant not found.',
            code: $variant ? 200 : 404,
            data: $variant?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductVariantRequest $request, string $sku)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        [$isUpdated, $updatedVariant, $updatedInventoryData] = $this->service->update($validatedData, $sku);

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Product variant updated successfully.'
                : 'Product variant not found.',
            code: $isUpdated ? 200 : 404,
            data: array_merge(
                $updatedVariant?->only(self::API_FIELDS) ?? [],
                $updatedInventoryData?->only(self::INVENTORY_FIELDS) ?? []
            )
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $sku)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $isDeleted = $this->repository->delete(
            idOrCriteria: fn($query) => $query->where('sku', $sku)
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Product variant deleted successfully.'
                : 'Product variant not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
