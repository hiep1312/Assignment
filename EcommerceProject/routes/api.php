<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function() {
    /* Auth */
    Route::name('auth.')->controller(AuthController::class)->group(function() {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
        Route::post('/logout', 'logout')->name('logout');
    });

    /* Data */
    Route::middleware('auth:sanctum')->group(function() {
        Route::apiResources([

        ]);
    });
});
