<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BlogCommentController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CartItemController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderItemController;
use App\Http\Controllers\Api\OrderShippingController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductReviewController;
use App\Http\Controllers\Api\ProductVariantController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Controllers\Api\UserController;
use App\Services\StripeService;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function() {
    /* Auth */
    Route::name('auth.')->controller(AuthController::class)->group(function() {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
        Route::post('/logout', 'logout')->name('logout');
        Route::post('/refresh', 'refresh')->name('refresh');

        Route::middleware('auth:jwt')->group(function() {
            Route::get('/me', 'me')->name('me');
        });
    });

    /* Data */
    Route::middleware(['auth:jwt', 'throttle:api'])->group(function() {
        /* Resources accessible only by authenticated users */
        Route::apiResources([
            'user-addresses' => UserAddressController::class,
            'images' => ImageController::class,
        ]);

        /* Resources accessible by guests for index/show but other actions require authentication */
        Route::apiResources([
            'categories' => CategoryController::class,
            'products' => ProductController::class,
            'banners' => BannerController::class,
            'blogs' => BlogController::class,
        ], [
            'excluded_middleware_for' => [
                'index' => ['auth:jwt'],
                'show' => ['auth:jwt']
            ]
        ]);

        /* Related resources also accessible by guests for index/show */
        Route::apiResources([
            'products.variants' => ProductVariantController::class,
            'products.reviews' => ProductReviewController::class,
            'blogs.comments' => BlogCommentController::class
        ], [
            'shallow' => true,
            'excluded_middleware_for' => [
                'index' => ['auth:jwt'],
                'show' => ['auth:jwt']
            ]
        ]);

        /* Products Reviews Distribution */
        Route::get('/products/reviews/distribution', [ProductReviewController::class, 'distribution'])
            ->name('products.reviews.distribution')
            ->withoutMiddleware('auth:jwt');

        /* Profile */
        Route::apiSingleton('profile', UserController::class)
            ->destroyable();

        /* Orders */
        Route::group([], function() {
            Route::apiResource('orders', OrderController::class)
                ->only(['index', 'show', 'update']);

            /* Related Orders */
            Route::apiResource('orders.items', OrderItemController::class)
                ->only(['index', 'show']);
            Route::apiSingletons([
                'orders.shipping-address' => OrderShippingController::class,
                'orders.payment' => PaymentController::class
            ], [
                'only' => ['show']
            ]);
        });

        /* Cart & Cart Items */
        Route::withoutMiddleware(['auth:jwt'])->group(function() {
            Route::apiSingleton('cart', CartController::class)
                ->creatable();
            Route::apiResource('carts.items', CartItemController::class)
                ->shallow();
            Route::delete('/carts/{cart}/items', [CartController::class, 'deleteItems'])
                ->name('carts.items.delete');
            Route::post('/carts/refresh', [CartController::class, 'refresh'])
                ->name('carts.refresh')
                ->withoutMiddleware('throttle:api')
                ->middleware('throttle:cart-refresh');
        });

        /* Checkout */
        Route::prefix('/checkout')->controller(CheckoutController::class)->name('checkout.')->group(function() {
            Route::post('/', 'create')->name('create');
            Route::put('/{order}', 'update')->name('update');
            Route::post('/cancel/{order}', 'cancel')->name('cancel');
            Route::post('/finalize/{order}', 'finalize')->name('finalize');
        });
    });

    /* Webhooks Payment */
    Route::post('/webhooks/payment', [StripeService::class, 'handleWebhook'])->name('webhooks.payment');
});
