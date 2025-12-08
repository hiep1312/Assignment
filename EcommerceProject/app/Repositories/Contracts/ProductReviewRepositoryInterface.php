<?php

namespace App\Repositories\Contracts;

interface ProductReviewRepositoryInterface extends RepositoryInterface
{
    /**
     * Check if a user has purchased a specific product.
     *
     * @param int $productId The ID of the product to check for purchase history
     * @param int|null $userId Optional user ID to check. If null, uses the currently authenticated user's ID
     *
     * @return bool Returns true if the user has purchased the product, false otherwise
     */
    public function hasUserPurchasedProduct($productId, $userId = null);

    /**
     * Get the distribution of ratings for a specific product.
     *
     * @param int $productId The ID of the product to get rating distribution for
     *
     * @return \Illuminate\Support\Collection A collection of objects containing:
     *                         - rating: The rating value
     *                         - total: The count of reviews with that rating
     *                         Results are ordered by rating in descending order
     */
    public function getRatingDistribution($productId);

    /**
     * Retrieve distribution statistics of products grouped by their rounded average rating.
     *
     * @return Collection A collection of objects containing:
     *                    - rating: The rounded average rating (0-5)
     *                    - product_ids: A JSON array of product IDs belonging to that rating group
     *                    - total_products: Number of products with that average rating
     *                    Ordered by rating in descending order
     */
    public function getProductRatingDistribution();
}
