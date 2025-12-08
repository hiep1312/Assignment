<?php

namespace App\Services;

use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use Illuminate\Database\QueryException;

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
}
