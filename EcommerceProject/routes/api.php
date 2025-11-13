<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ImageController;
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
        Route::apiSingleton('profile', UserController::class, ['except' => ['store']]);

        /* Resources */
        Route::apiResources([
            'user-addresses' => UserAddressController::class,
            'categories' => CategoryController::class,
            'images' => ImageController::class,
            'products' => ProductController::class,
        ]);

        /* Related Products */
        /* Route::apiResources([
            'products.variants' => ProductVariantController::class,
            'products.reviews' => ProductReviewController::class
        ], [
            'bindingFields' => [
                'product' => 'slug',
                'variant' => 'sku',
                'review' => 'id'
            ],
            'shallow' => true,
        ]); */
    });
});
