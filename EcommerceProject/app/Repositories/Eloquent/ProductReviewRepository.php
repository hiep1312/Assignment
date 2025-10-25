<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductReview;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;

class ProductReviewRepository extends BaseRepository implements ProductReviewRepositoryInterface
{
    public function getModel()
    {
        return ProductReview::class;
    }
}
