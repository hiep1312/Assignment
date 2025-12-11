<?php

namespace App\Http\Controllers\Api;

use App\Enums\UserRole;
use App\Helpers\ApiQueryRelation;
use App\Http\Requests\Client\ProductReviewRequest;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use App\Services\ProductReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends BaseApiController
{
    use ApiQueryRelation;

    const API_FIELDS = ['id', 'product_id', 'user_id', 'rating', 'content', 'created_at'];

    protected function getAllowedRelationsWithFields(): array
    {
        return [
            'user' => UserController::API_FIELDS
        ];
    }

    public function __construct(
        protected ProductReviewRepositoryInterface $repository,
        protected ProductReviewService $service
    ){}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, string $productId)
    {
        $reviews = $this->repository->getAll(
            criteria: function(&$query) use ($request, $productId) {
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
                )->when(
                    $request->boolean('with_trashed'),
                    fn($innerQuery) => $innerQuery->withTrashed()
                )->when(
                    $request->boolean('exclude_my_review') && Auth::guard('jwt')->check(),
                    fn($innerQuery) => $innerQuery->whereNot('user_id', authPayload('sub'))
                );

                $query->where('product_id', $productId);
            },
            perPage: $this->getPerPage($request),
            columns: array_merge(
                self::API_FIELDS,
                $request->boolean('with_trashed') ? ['deleted_at'] : []
            ),
            pageName: 'page'
        );

        return $this->response(
            success: true,
            message: 'Product review list retrieved successfully.',
            additionalData: array_merge(
                $reviews->withQueryString()->toArray(),
                $request->boolean('with_rating_stats') ? ['rating_distribution' => $this->repository->getRatingDistribution($productId)] : [],
                $request->boolean('with_can_review') ? ['can_review' => $this->repository->hasUserPurchasedProduct($productId)] : [],
                $request->boolean('with_my_review') ? ['my_review' => $this->repository->getFirstUserReview()] : []
            )
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductReviewRequest $request, string $productId)
    {
        $validatedData = $request->validated();
        [$isCreated, $createdReview] = $this->service->create($validatedData, $productId);

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
    public function update(ProductReviewRequest $request, string $id)
    {
        $validatedData = $request->validated();
        $isUpdated = $this->repository->update(
            idOrCriteria: fn($query) => $query->where('id', $id)
                ->where('user_id', authPayload('sub')),
            attributes: $validatedData,
            updatedModel: $updatedReview
        );
        $updatedReview = $updatedReview->first();

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
        ['role' => $role, 'sub' => $userId] = authPayload();

        $isDeleted = $this->repository->delete(
            idOrCriteria: function($query) use ($id, $role, $userId){
                $query->where('id', $id)
                    ->when($role === UserRole::USER->value, fn($innerQuery) => $innerQuery->where('user_id', $userId));
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

    public function distribution()
    {
        $ratingDistribution = $this->repository->getProductRatingDistribution();

        return $this->response(
            success: true,
            message: 'Product rating distribution retrieved successfully.',
            data: $ratingDistribution->map(fn($item) => array_merge((array) $item, ['product_ids' => json_decode($item->product_ids)]))->toArray()
        );
    }
}
