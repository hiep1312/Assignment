<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\ProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'title', 'slug', 'description', 'status', 'created_at'];
    const MAIN_IMAGE_PRIVATE_FIELDS = ['images.id', 'images.image_url', 'images.created_at', 'imageables.imageable_id', 'imageables.imageable_type'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'variants' => (object)[
                'fields' => ProductVariantController::API_FIELDS,
                'inventory' => ProductVariantController::INVENTORY_FIELDS,
            ],
            'reviews' => ProductReviewController::API_FIELDS,
            'images' => ImageController::API_FIELDS,
            'categories' => CategoryController::API_FIELDS
        ];
    }

    protected function getAllowedAggregateRelations(): array
    {
        return [
            'avg' => 'reviews.rating',
        ];
    }

    public function __construct(
        protected ProductRepositoryInterface $repository,
        protected ProductService $service
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $products = $this->repository->getAll(
            criteria: function(&$query) use ($request) {
                $this->getRequestedAggregateRelations($request, $query)
                    ->with(['mainImage:' . (implode(',', self::MAIN_IMAGE_PRIVATE_FIELDS)), ...$this->getRequestedRelations($request)]);

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
                    function($innerQuery) use ($request){
                        $innerQuery->whereHas('categories', function($subQuery) use ($request){
                            $subQuery->where('categories.slug', $request->category)
                                ->orWhere('categories.id', $request->category);
                        });
                    }
                );
            },
            perPage: $this->getPerPage($request),
            columns: self::API_FIELDS,
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Product list retrieved successfully.',
            additionalData: $products->withQueryString()->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        [$createdProduct] = $this->service->create($validatedData);

        return $this->response(
            success: true,
            message: 'Product created successfully.',
            code: 201,
            data: $createdProduct->only([...self::API_FIELDS, 'images', 'categories'])
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        $product = $this->repository->first(
            criteria: function($query) use ($request, $slug){
                $this->getRequestedAggregateRelations($request, $query)
                    ->with(['mainImage:' . (implode(',', self::MAIN_IMAGE_PRIVATE_FIELDS)), ...$this->getRequestedRelations($request)])
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
            data: $product?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, string $slug)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $validatedData = $request->validated();
        [$isUpdated, $updatedProduct] = $this->service->update($validatedData, $slug);

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Product updated successfully.'
                : 'Product not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedProduct?->only([...self::API_FIELDS, 'images', 'categories']) ?? [],
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        if(!$this->authorizeRole()) return $this->forbiddenResponse();

        $isDeleted = $this->repository->delete(
            idOrCriteria: fn($query) => $query->where('slug', $slug)
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Product deleted successfully.'
                : 'Product not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
