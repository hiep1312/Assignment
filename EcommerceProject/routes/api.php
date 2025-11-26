<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BannerController;
use App\Http\Controllers\Api\BlogCommentController;
use App\Http\Controllers\Api\BlogController;
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
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function() {
    /* Auth */
    Route::name('auth.')->controller(AuthController::class)->group(function() {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
        Route::post('/logout', 'logout')->name('logout');
    });

    /* Data */
    Route::middleware('auth:jwt')->group(function() {
        /* User */
        Route::apiSingleton('profile', UserController::class, ['destroyable' => true]);

        /* Resources */
        Route::apiResources([
            'user-addresses' => UserAddressController::class,
            'categories' => CategoryController::class,
            'images' => ImageController::class,
            'products' => ProductController::class,
            'orders' => OrderController::class,
            'banners' => BannerController::class,
            'blogs' => BlogController::class
        ]);

        /* Related Products & Blogs */
        Route::apiResources([
            'products.variants' => ProductVariantController::class,
            'products.reviews' => ProductReviewController::class,
            'blogs.comments' => BlogCommentController::class
        ], [
            'shallow' => true,
        ]);

        /* Related Orders */
        Route::apiResource('orders.items', OrderItemController::class);
        Route::apiSingletons([
            'orders.shipping-address' => OrderShippingController::class,
            'orders.payment' => PaymentController::class
        ], ['creatable' => true]);

        /* Checkout */
        Route::prefix('/checkout')->controller(CheckoutController::class)->name('checkout.')->group(function() {
            Route::post('/', 'create')->name('create');
            Route::put('/{order}', 'update')->name('update');
            Route::post('/cancel/{order}', 'cancel')->name('cancel');
            Route::post('/finalize/{order}', 'finalize')->name('finalize');
        });
    });
});
