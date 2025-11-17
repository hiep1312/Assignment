<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\ProductReviewRequest;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'product_id', 'user_id', 'rating', 'content', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'product' => ProductController::API_FIELDS,
            'user' => UserController::API_FIELDS
        ];
    }

    public function __construct(
        protected ProductReviewRepositoryInterface $repository,
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $slugProduct)
    {
        $reviews = $this->repository->getAll(
            criteria: function(&$query) use ($request, $slugProduct) {
                $query->with($this->getRequestedRelations($request));

                $query->when(isset($request->search), function($innerQuery) use ($request){
                    $innerQuery->whereLike('content', '%'. trim($request->search) .'%');
                })->when(
                    isset($request->rating),
                    function($innerQuery) use ($request){
                        $ratingRange = explode('-', $request->rating, 2);
                        if(count($ratingRange) === 1) {
                            $innerQuery->where('rating', $ratingRange[0]);
                        }else {
                            [$minRating, $maxRating] = $ratingRange;
                            $innerQuery->whereBetween('rating', [$minRating, $maxRating]);
                        }
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
            message: 'Product review list retrieved successfully.',
            additionalData: $reviews->toArray()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductReviewRequest $request, string $slugProduct)
    {
        $validatedData = $request->validated();
        $isCreated = $this->repository->createByProductSlug(
            attributes: $validatedData + ['user_id' => $request->user('jwt')->id],
            slug: $slugProduct,
            createdModel: $createdReview
        );

        return $this->response(
            success: (bool) $isCreated,
            message: $isCreated
                ? 'Product review created successfully.'
                : 'Failed to create product review.',
            code: $isCreated ? 201 : 400,
            data: $createdReview?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $review = $this->repository->first(
            criteria: function($query) use ($request, $id){
                $query->with($this->getRequestedRelations($request))
                    ->where('id', $id);
            },
            columns: self::API_FIELDS,
            throwNotFound: false
        );

        return $this->response(
            success: (bool) $review,
            message: $review
                ? 'Product review retrieved successfully.'
                : 'Product review not found.',
            code: $review ? 200 : 404,
            data: $review?->toArray() ?? []
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductReviewRequest $request)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: $request->id ?? self::INVALID_ID,
            attributes: $validatedData,
            updatedModel: $updatedReview
        );

        return $this->response(
            success: (bool) $isUpdated,
            message: $isUpdated
                ? 'Product review updated successfully.'
                : 'Product review not found.',
            code: $isUpdated ? 200 : 404,
            data: $updatedReview?->only(self::API_FIELDS) ?? []
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        ['role' => $role, 'sub' => $userId] = Auth::guard('jwt')->payload()->toArray();

        $isDeleted = $this->repository->delete(
            idOrCriteria: function($query) use ($id, $role, $userId){
                $query->where('id', $id)
                    ->when($role === 'user', fn($innerQuery) => $innerQuery->where('user_id', $userId));
            }
        );

        return $this->response(
            success: (bool) $isDeleted,
            message: $isDeleted
                ? 'Product review deleted successfully.'
                : 'Product review not found.',
            code: $isDeleted ? 200 : 404
        );
    }
}
