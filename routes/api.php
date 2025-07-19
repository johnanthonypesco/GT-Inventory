<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffLocationController;
use App\Http\Controllers\Mobile\MobileChatController;
use App\Http\Controllers\Mobile\MobileOrderController;
use App\Http\Controllers\Mobile\MobileStaffAuthController;
use App\Http\Controllers\Mobile\MobileOrderHistoryController;
use App\Http\Controllers\Mobile\MobileCustomerAccountController;
use App\Http\Controllers\Auth\MobileAuthenticatedSessionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| This file is organized into two main groups: 'customer' and 'staff',
| to clearly separate their respective functionalities.
|
*/

Route::prefix('mobile')->group(function () {

    // --- Customer Routes ---
    Route::prefix('customer')->group(function () {
        // Customer Authentication
        Route::post('/login', [MobileAuthenticatedSessionController::class, 'store']);
        Route::post('/verify-2fa', [MobileAuthenticatedSessionController::class, 'verify2FA']);

        // Authenticated Customer Routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', [MobileAuthenticatedSessionController::class, 'user']);
            Route::post('/logout', [MobileAuthenticatedSessionController::class, 'destroy']);
            
            // Other customer-specific routes
            Route::post('/orders', [MobileOrderController::class, 'storeOrder']);
            Route::get('/exclusive-deals', [MobileOrderController::class, 'index']);
            Route::get('/user/orders', [MobileOrderController::class, 'getUserOrders']);
            Route::get('/contacts', [MobileChatController::class, 'getConversations']);
            Route::get('/messages/{id}/{type}', [MobileChatController::class, 'getMessages']);
            Route::post('/send', [MobileChatController::class, 'sendMessage']);
            Route::post('/mark-as-read', [MobileChatController::class, 'markAsRead']);
            Route::get('/order-history', [MobileOrderHistoryController::class, 'getOrderHistory']);
            Route::get('/order-details/{orderId}', [MobileOrderHistoryController::class, 'getOrderDetails']);
            Route::get('/account', [MobileCustomerAccountController::class, 'getAccount']);
            Route::post('/account/update', [MobileCustomerAccountController::class, 'updateAccount']);
        });
    });

    // --- Staff Routes ---
    Route::prefix('staff')->group(function () {
        // Staff Authentication
        Route::post('/login', [MobileStaffAuthController::class, 'login']);
        Route::post('/verify-2fa', [MobileStaffAuthController::class, 'verifyTwoFactor']);

        // Authenticated Staff Routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', [MobileStaffAuthController::class, 'user']);
            Route::post('/logout', [MobileStaffAuthController::class, 'logout']);
            Route::post('/location-update', [StaffLocationController::class, 'updateLocation']);
        });
    });
});