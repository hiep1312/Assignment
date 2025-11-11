<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
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
        Route::apiResources([
            'products' => ProductController::class
        ]);
    });
});
