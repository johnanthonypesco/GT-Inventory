<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// claer

use App\Http\Controllers\Mobile\MobileChatController;
use App\Http\Controllers\Mobile\MobileOrderController;
use App\Http\Controllers\Mobile\MobileOrderHistoryController;
use App\Http\Controllers\Mobile\MobileCustomerAccountController;
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
        Route::get('/contacts', [MobileChatController::class, 'getConversations']);
        Route::get('/messages/{id}/{type}', [MobileChatController::class, 'getMessages']);
        Route::post('/send', [MobileChatController::class, 'sendMessage']);
        Route::post('/mark-as-read', [MobileChatController::class, 'markAsRead']);
        Route::get('/order-history', [MobileOrderHistoryController::class, 'getOrderHistory']);
        Route::get('/order-details/{orderId}', [MobileOrderHistoryController::class, 'getOrderDetails']); // ✅ Fetch order details
        Route::get('/customer/account', [MobileCustomerAccountController::class, 'getAccount']);
        Route::post('/customer/account/update', [MobileCustomerAccountController::class, 'updateAccount']);
    });
  

});

