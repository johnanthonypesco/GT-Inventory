<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// claer

use App\Http\Controllers\Mobile\MobileOrderController;
use App\Http\Controllers\Auth\MobileAuthenticatedSessionController;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthenticatedSessionController::class, 'store']);
    Route::post('/verify-2fa', [MobileAuthenticatedSessionController::class, 'verify2FA']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [MobileAuthenticatedSessionController::class, 'user']);
        Route::post('/logout', [MobileAuthenticatedSessionController::class, 'destroy']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/orders', [MobileOrderController::class, 'storeOrder']);

        Route::get('/exclusive-deals', [MobileOrderController::class, 'index']);
        Route::get('/user/orders', [MobileOrderController::class, 'getUserOrders']);
    });
  

});

