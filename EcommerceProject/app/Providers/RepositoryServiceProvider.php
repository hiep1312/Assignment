<?php

namespace App\Providers;

use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Repositories\Contracts\BlogCommentRepositoryInterface;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\CartItemRepositoryInterface;
use App\Repositories\Contracts\CartRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ImageableRepositoryInterface;
use App\Repositories\Contracts\ImageRepositoryInterface;
use App\Repositories\Contracts\MailRepositoryInterface;
use App\Repositories\Contracts\MailUserRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\OrderItemRepositoryInterface;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\OrderShippingRepositoryInterface;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ProductReviewRepositoryInterface;
use App\Repositories\Contracts\ProductVariantInventoryRepositoryInterface;
use App\Repositories\Contracts\ProductVariantRepositoryInterface;
use App\Repositories\Contracts\UserAddressRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\BannerRepository;
use App\Repositories\Eloquent\BlogCommentRepository;
use App\Repositories\Eloquent\BlogRepository;
use App\Repositories\Eloquent\CartItemRepository;
use App\Repositories\Eloquent\CartRepository;
use App\Repositories\Eloquent\CategoryRepository;
use App\Repositories\Eloquent\ImageableRepository;
use App\Repositories\Eloquent\ImageRepository;
use App\Repositories\Eloquent\MailRepository;
use App\Repositories\Eloquent\MailUserRepository;
use App\Repositories\Eloquent\NotificationRepository;
use App\Repositories\Eloquent\OrderItemRepository;
use App\Repositories\Eloquent\OrderRepository;
use App\Repositories\Eloquent\OrderShippingRepository;
use App\Repositories\Eloquent\PaymentRepository;
use App\Repositories\Eloquent\ProductRepository;
use App\Repositories\Eloquent\ProductReviewRepository;
use App\Repositories\Eloquent\ProductVariantInventoryRepository;
use App\Repositories\Eloquent\ProductVariantRepository;
use App\Repositories\Eloquent\UserAddressRepository;
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
        ProductReviewRepositoryInterface::class => ProductReviewRepository::class,
        ImageableRepositoryInterface::class => ImageableRepository::class,
        MailRepositoryInterface::class => MailRepository::class,
        OrderRepositoryInterface::class => OrderRepository::class,
        OrderItemRepositoryInterface::class => OrderItemRepository::class,
        OrderShippingRepositoryInterface::class => OrderShippingRepository::class,
        PaymentRepositoryInterface::class => PaymentRepository::class,
        NotificationRepositoryInterface::class => NotificationRepository::class,
        MailUserRepositoryInterface::class => MailUserRepository::class,
        BlogRepositoryInterface::class => BlogRepository::class,
        BlogCommentRepositoryInterface::class => BlogCommentRepository::class,
        UserAddressRepositoryInterface::class => UserAddressRepository::class,
        CartRepositoryInterface::class => CartRepository::class,
        CartItemRepositoryInterface::class => CartItemRepository::class,
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
