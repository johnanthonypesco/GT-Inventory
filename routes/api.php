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
use App\Http\Controllers\Mobile\MobileStaffDashboardController;
use App\Http\Controllers\mobile\MobileStaffOrdersController;
use App\Http\Controllers\mobile\MobileStaffQrController;
use App\Http\Controllers\mobile\MobileStaffChatController;
use App\Http\Controllers\mobile\MobileGroupChatController;
use App\Http\Controllers\mobile\MobileCustomerReview;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('mobile')->group(function () {

    // --- Customer Routes ---
    Route::prefix('customer')->group(function () {
        Route::post('/login', [MobileAuthenticatedSessionController::class, 'store']);
        Route::post('/verify-2fa', [MobileAuthenticatedSessionController::class, 'verify2FA']);

        // --- ADD THESE NEW ROUTES ---
        Route::post('/resend-email', [MobileAuthenticatedSessionController::class, 'resendEmail']);
        Route::post('/resend-sms', [MobileAuthenticatedSessionController::class, 'resendSms']);


        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', [MobileAuthenticatedSessionController::class, 'user']);
            Route::post('/logout', [MobileAuthenticatedSessionController::class, 'destroy']);
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
            Route::post('/review/store', [MobileCustomerReview::class, 'store']);
        });
    });

    // --- Staff Routes ---
    Route::prefix('staff')->group(function () {
        // Authenticated Staff Routes
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/user', [MobileStaffAuthController::class, 'user']);
            Route::post('/logout', [MobileStaffAuthController::class, 'logout']);
            Route::post('/location-update', [StaffLocationController::class, 'updateLocation']);
            Route::get('/dashboard-stats', [MobileStaffDashboardController::class, 'getDashboardStats']);
            Route::get('/orders', [MobileStaffOrdersController::class, 'index']);
            
            // This is the route for the QR Code scanner
            Route::post('/process-scan', [MobileStaffQrController::class, 'processScannedOrder']);
             Route::post('/order/{order}/update-status', [MobileStaffOrdersController::class, 'updateProductStatus']);

              // ✅ ADD THESE NEW CHAT ROUTES FOR STAFF
            Route::get('/chat/conversations', [MobileStaffChatController::class, 'getConversations']);
            Route::get('/chat/messages/{id}/{type}', [MobileStaffChatController::class, 'getMessages']);
            Route::post('/chat/send-message', [MobileStaffChatController::class, 'sendMessage']);

            // ✅ ADD THESE NEW GROUP CHAT ROUTES
            Route::get('/chat/group', [MobileGroupChatController::class, 'getGroupMessages']);
            Route::post('/chat/group/send', [MobileGroupChatController::class, 'sendGroupMessage']);
        });
        
        // Staff Authentication
        Route::post('/login', [MobileStaffAuthController::class, 'login']);
        Route::post('/verify-2fa', [MobileStaffAuthController::class, 'verifyTwoFactor']);

         // ✅ ADD THESE NEW ROUTES FOR RESENDING CODES
        Route::post('/send-2fa-sms', [MobileStaffAuthController::class, 'sendTwoFactorSms']);
        Route::post('/resend-2fa-email', [MobileStaffAuthController::class, 'resendTwoFactorEmail']);
    });
});