<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\ProductReviewRequest;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    const API_FIELDS = ['id', 'user_id', 'rating', 'content', 'created_at'];

    public function __construct(
        protected ProductReviewRepositoryInterface $repository,
        protected ProductRepositoryInterface $productRepository
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slugProduct)
    {
        $reviews = $this->repository->getAll(
            criteria: function(&$query) use ($request, $slugProduct) {
                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->whereLike('content', '%'. trim($request->search) .'%');
                })->when(
                    isset($request->rating),
                    function($innerQuery) use ($request){
                        $ratingRange = explode('-', $request->rating, 2);
                        if(count($ratingRange) === 1) {
                            $innerQuery->where('rating', $ratingRange[0]);
                        }else {
                            [$minStar, $maxStar] = $ratingRange;
                            $innerQuery->whereBetween('rating', [$minStar, $maxStar]);
                        }
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
            'message' => 'Product review list retrieved successfully.',
            ...$reviews->toArray()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductReviewRequest $request, string $slugProduct)
    {
        if(!($product = $this->productRepository->first(fn($query) => $query->where('slug', $slugProduct)))){
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        $validatedData = $request->validated();
        $review = $product->reviews()->create($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Product review created successfully.',
            'data' => $review->only(self::API_FIELDS),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $review = $this->repository->find(
            idOrCriteria: $id,
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return response()->json([
            'success' => (bool) $review,
            'message' => $review ? 'Product review retrieved successfully.' : 'Product review not found.',
            'data' => $review?->only(self::API_FIELDS),
        ], $review ? 200 : 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductReviewRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: $request->id,
            attributes: $validatedData,
            updatedModel: $updatedReview
        );

        return response()->json([
            'success' => (bool) $isUpdated,
            'message' => $isUpdated
                ? 'Product review updated successfully.'
                : 'Product review not found.',
            'data' => $updatedReview?->only(self::API_FIELDS),
        ], $isUpdated ? 200 : 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $isDeleted = $this->repository->delete(
            idOrCriteria: $id,
        );

        return response()->json([
            'success' => (bool) $isDeleted,
            'message' => $isDeleted
                ? 'Product review deleted successfully.'
                : 'Product review not found.',
        ], $isDeleted ? 200 : 404);
    }
}
