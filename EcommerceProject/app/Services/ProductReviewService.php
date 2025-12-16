<?php

namespace App\Services;

use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ProductReviewService
{
    public function __construct(
        protected ProductReviewRepositoryInterface $repository,
    ){}

    public function create(array $data, string $productId): array
    {
        try {
            $createdReview = $this->repository->create(
                attributes: array_merge(
                    $data,
                    ['product_id' => $productId, 'user_id' => authPayload('sub')]
                )
            );

            return [true, $createdReview];

        }catch(QueryException $queryException) {
            return [false, null];
        }
    }

    public function update(array $data, string $id): array
    {
        $isUpdated = $this->repository->update(
            idOrCriteria: fn($query) => $query->where('id', $id)
                ->where('user_id', authPayload('sub')),
            attributes: array_merge(
                $data,
                array_key_exists('content', $data)
                    ? ['content' => DB::raw("IF(deleted_at IS NULL, ". DB::getPdo()->quote($data['content']) .", content)")]
                    : []
            ),
            rawEnabled: true,
            updatedModel: $updatedReview,
        );

        $updatedReview = $updatedReview->first();
        if($updatedReview && array_key_exists('content', $data) && !$updatedReview->deleted_at) {
            $updatedReview->content = $data['content'];
        }

        return [(bool) $isUpdated, $updatedReview];
    }
}
