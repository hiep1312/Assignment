<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\ProductRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
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
            'primaryVariant' => ProductVariantController::API_FIELDS,
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
            'count' => 'reviews',
            'sum' => ['inventories.stock', 'inventories.reserved', 'inventories.sold_number'],
            'avg' => 'reviews.rating'
        ];
    }

    public function __construct(
        protected ProductRepositoryInterface $repository,
        protected ProductVariantRepositoryInterface $variantRepository,
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
                        $subQuery->whereLike('title', '%'. trim($request->search) .'%');
                    });
                })->when(
                    isset($request->status),
                    fn($innerQuery) => $innerQuery->where('status', $request->status)
                )->when(
                    isset($request->category),
                    function($innerQuery) use ($request){
                        $innerQuery->whereHas('categories', function($subQuery) use ($request){
                            $subQuery->where('categories.slug', $request->category);
                        });
                    }
                )->when(
                    isset($request->filter_categories) && empty($request->category),
                    function($innerQuery) use ($request){
                        $categories = is_array($request->filter_categories) ? $request->filter_categories : preg_split('/\s*,\s*/', $request->filter_categories);

                        $innerQuery->whereHas('categories', function($subQuery) use ($categories){
                            $subQuery->whereIn('categories.id', $categories);
                        });
                    }
                )->when(
                    isset($request->filter_availability),
                    function($innerQuery) use ($request){
                        $availabilityFilters = is_array($request->filter_availability) ? $request->filter_availability : preg_split('/\s*,\s*/', $request->filter_availability);
                        $availabilityConditions = [
                            'in_stock' => fn($subQuery) => $subQuery->havingRaw("COALESCE(inventories_sum_stock, 0) > 0"),
                            'new_arrival' => fn($subQuery) => $subQuery->where('created_at', '>=', now()->subDays(7)),
                        ];

                        foreach($availabilityFilters as $availabilityKey) {
                            if(isset($availabilityConditions[$availabilityKey])) {
                                $availabilityConditions[$availabilityKey]($innerQuery);
                            }
                        }
                    }
                )->when(
                    isset($request->filter_ids),
                    function($innerQuery) use ($request){
                        $productIds = is_array($request->filter_ids) ? $request->filter_ids : preg_split('/\s*,\s*/', $request->filter_ids);

                        $innerQuery->whereIn('id', $productIds);
                    }
                );

                $primaryPriceSubquery = <<<SQL
                    (SELECT MIN(COALESCE(discount, price))
                    FROM product_variants
                    WHERE product_variants.product_id = products.id
                    AND deleted_at IS NULL
                    AND status = 1)
                SQL;

                $sortOptions = [
                    'PRICE_ASC' => 'price-asc',
                    'PRICE_DESC' => 'price-desc',
                    'NEWEST' => 'newest'
                ];

                $query->when(
                    isset($request->price_range),
                    function($innerQuery) use ($request, $primaryPriceSubquery){
                        $priceRange = is_array($request->price_range) ? $request->price_range : preg_split('/\s*-\s*/', $request->price_range, 2);
                        $minPrice = is_numeric($priceRange[0]) ? (int) $priceRange[0] : 0;
                        $maxPrice = is_numeric($priceRange[1] ?? null) ? (int) $priceRange[1] : PHP_INT_MAX;

                        $innerQuery->whereRaw("{$primaryPriceSubquery} BETWEEN ? AND ?", [$minPrice, $maxPrice]);
                    }
                )->when(
                    isset($request->sort_by) && in_array($request->sort_by, array_values($sortOptions), true),
                    function($innerQuery) use ($request, $primaryPriceSubquery, $sortOptions){
                        match($request->sort_by) {
                            $sortOptions['PRICE_ASC'] => $innerQuery->orderByRaw("{$primaryPriceSubquery} ASC"),
                            $sortOptions['PRICE_DESC'] => $innerQuery->orderByRaw("{$primaryPriceSubquery} DESC"),
                            $sortOptions['NEWEST'] => $innerQuery->orderBy('created_at', 'DESC')
                        };
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
            additionalData: array_merge(
                $products->withQueryString()->toArray(),
                $request->boolean('with_price_range') ? ['price_range' => $this->variantRepository->getPriceRange()] : []
            ),
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
