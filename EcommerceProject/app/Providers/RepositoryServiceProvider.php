<?php

namespace App\Providers;

use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use App\Repositories\Contracts\ProductVariantInventoryRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\BannerRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\ImageRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductReviewRepository;
use App\Repositories\Eloquent\ProductVariantInventoryRepository;
use App\Repositories\Eloquent\ProductVariantRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        UserRepositoryInterface::class => UserRepository::class,
        BannerRepositoryInterface::class => BannerRepository::class,
        ImageRepositoryInterface::class => ImageRepository::class,
        ProductVariantRepositoryInterface::class => ProductVariantRepository::class,
        ProductRepositoryInterface::class => ProductRepository::class,
        CategoryRepositoryInterface::class => CategoryRepository::class,
        ProductVariantInventoryRepositoryInterface::class => ProductVariantInventoryRepository::class,
        ProductReviewRepositoryInterface::class => ProductReviewRepository::class
    ];

    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
    }
}
