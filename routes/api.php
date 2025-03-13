<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// claer

use App\Http\Controllers\Auth\MobileAuthenticatedSessionController;

Route::prefix('mobile')->group(function () {
    Route::post('/login', [MobileAuthenticatedSessionController::class, 'store']);
    Route::post('/verify-2fa', [MobileAuthenticatedSessionController::class, 'verify2FA']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [MobileAuthenticatedSessionController::class, 'user']);
        Route::post('/logout', [MobileAuthenticatedSessionController::class, 'destroy']);
    });
});

