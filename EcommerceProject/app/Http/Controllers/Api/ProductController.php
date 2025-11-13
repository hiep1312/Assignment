<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Client\ProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    const API_FIELDS = ['id', 'title', 'slug', 'description', 'status', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'variants' => (object)[
                'fields' => ProductVariantController::API_FIELDS,
                'inventory' => ProductVariantController::INVENTORY_FIELDS,
            ],
            'reviews' => ProductReviewController::API_FIELDS
        ];
    }

    protected function getAllowedAggregateRelations(): array
    {
        return [
            'avg' => 'reviews.rating',
        ];
    }

    public function __construct(
        protected ProductRepositoryInterface $repository
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $this->getRequestedAggregateRelations($request, $query)
                    ->with(['mainImages', ...$this->getRequestedRelations($request)]);

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->where(function($subQuery) use ($request){
                        $subQuery->whereLike('title', '%'. trim($request->search) .'%')
                            ->orWhereLike('description', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->status),
                    fn($innerQuery) => $innerQuery->where('status', $request->status)
                )->when(
                    isset($request->category),
                    fn($innerQuery) => $innerQuery->whereHas('categories', function($subQuery) use ($request){
                        $subQuery->where('categories.slug', $request->category);
                    })
                );
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Product list retrieved successfully.',
            additionalData: $products->setHidden(['updated_at', 'deleted_at'])->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $validatedData = $request->validated();
        $createdProduct = $this->repository->create($validatedData);

        return $this->response(
            success: true,
            message: 'Product created successfully.',
            code: 201,
            data: $createdProduct->only(self::API_FIELDS)
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        $product = $this->repository->first(
            criteria: function($query) use ($request, $slug){
                $query->with($this->getRequestedRelations($request))
                    ->where('slug', $slug);
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $product,
            message: $product
                ? 'Product retrieved successfully.'
                : 'Product not found.',
            code: $product ? 200 : 404,
            data: $product?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $slug)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: $request->id,
            attributes: $validatedData,
            updatedModel: $updatedProduct
        );

        return response()->json([
            'success' => (bool) $isUpdated,
            'message' => $isUpdated
                ? 'Product updated successfully.'
                : 'Product not found.',
            'data' => $updatedProduct?->only(self::API_FIELDS),
        ], $isUpdated ? 200 : 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        $isDeleted = $this->repository->delete(
            idOrCriteria: fn($query) => $query->where('slug', $slug)
        );

        return response()->json([
            'success' => (bool) $isDeleted,
            'message' => $isDeleted
                ? 'Product deleted successfully.'
                : 'Product not found.',
        ], $isDeleted ? 200 : 404);
    }
}
