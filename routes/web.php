<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRCodeController;


// Admin Controller
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\ItemQrCodeController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\Admin\ChattingController;


// Staff Controller
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GroupChatController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\Customer\ChatRepsController;
use App\Http\Controllers\SuperAdminAccountController;

// Customer Controller
use App\Http\Controllers\Auth\TwoFactorAuthController;


//Super Admin Login
use App\Http\Controllers\Admin\ManageaccountController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\Admin\ProductlistingController;
use App\Http\Controllers\Customer\ManageorderController;
use App\Http\Controllers\Customer\CustomerloginController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;



use App\Http\Controllers\Auth\StaffAuthenticatedSessionController;

// chat
use App\Http\Controllers\Staff\ChatController as StaffChatController;
use App\Http\Controllers\Auth\SuperAdminAuthenticatedSessionController;
use App\Http\Controllers\Staff\LoginController as StaffLoginController;

// GroupChat
use App\Http\Controllers\Staff\OrderController as StaffOrderController;




use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Staff\HistoryController as StaffHistoryController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\InventoryController as StaffInventoryController;
use App\Http\Controllers\Customer\HistoryController as CustomerHistoryController;
use App\Http\Controllers\Customer\ManageaccountController as CustomerManageaccountController;




// ADMIN ROUTES
Route::middleware(['auth:superadmin,admin,staff'])->group(function () {
    // ONLY FOR THE ADMINS
    Route::middleware(['auth:superadmin,admin'])->group(function () {
        Route::get('admin/inventory/{location_filter?}', [InventoryController::class, 'showInventory'])->name('admin.inventory');
        Route::post('admin/inventory/', [InventoryController::class, 'showInventoryLocation'])->name('admin.inventory.location');
    
        Route::post('admin/inventory/register/product', [InventoryController::class, 'registerNewProduct'])->name('admin.register.product');
        Route::delete('admin/inventory/delete/product/{product}', [InventoryController::class, 'destroyProduct'])->name('admin.destroy.product');
    
        Route::post('admin/inventory/{addType}', [InventoryController::class, 'addStock'])->name('admin.inventory.store');
        Route::post('admin/inventory/search/{type}', [InventoryController::class, 'searchInventory'])->name('admin.inventory.search');
        Route::get('admin/inventory/export', [ExportController::class, 'export'])->name('admin.inventory.export');
    
        Route::get('admin/history', [HistoryController::class, 'showHistory'])->name('admin.history');
    
        Route::get('admin/productlisting/', [ProductlistingController::class, 'showProductListingPage'])->name('admin.productlisting');
        Route::post('admin/productlisting', [ProductlistingController::class, 'createExclusiveDeal'])->name('admin.productlisting.create');
        Route::delete('admin/productlisting/{deal_id}/{company}', [ProductlistingController::class, 'destroyExclusiveDeal'])->name('admin.productlisting.destroy');
    
        Route::get('admin/manageaccount', [ManageaccountController::class, 'showManageaccount'])->name('admin.manageaccount');

        // Route::get('/orders/{order}/generate-qr-code', [QRCodeController::class, 'generateOrderQrCode'])
        //     ->name('orders.generateQrCode');

        Route::get('/orders/{order}/show-qr-code', [QrCodeController::class, 'showOrderQrCode'])
            ->name('orders.showQrCode');

        Route::get('/scan-qr', function () {
            return view('orders.scan'); // Blade file for scanning QR codes
        })->name('orders.scan');

        Route::get('/upload-qr', function () {
            return view('orders.upload_qr'); // Blade file for uploading QR codes
        })->name('upload.qr');

        Route::post('/upload-qr-code', [InventoryController::class, 'uploadQrCode'])->name('upload.qr.code');

        Route::post('/deduct-inventory', [InventoryController::class, 'deductInventory']);
    });

    // AVAILABLE ROUTES EVEN FOR STAFF
    Route::get('admin/order', [OrderController::class, 'showOrder'])->name('admin.order');
    Route::put('admin/orders/{order}', [OrderController::class, 'updateOrder'])->name('admin.order.update');

    Route::get('admin/chat', [ChatController::class, 'showChat'])->name('admin.chat');
    Route::get('admin', [LoginController::class, 'showIndex'])->name('admin.index');
});


// LOGGED IN CUSTOMER ROUTES
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('customer/order', [CustomerOrderController::class, 'showOrder'])->name('customer.order');
    
    Route::get('customer/chat', [CustomerChatController::class, 'showChat'])->name('customer.chat');

    Route::get('customer/manageorder', [ManageorderController::class, 'showManageOrder'])->name('customer.manageorder');
    
    Route::get('customer/history', [CustomerHistoryController::class, 'showHistory'])->name('customer.history');

    Route::get('customer/manageaccount', [CustomerManageaccountController::class, 'showAccount'])->name('customer.manageaccount');

    // Route::post('/superadmin/logout', [SuperAdminAuthenticatedSessionController::class, 'destroy'])->name('superadmin.logout');
    // Route::post('/admin/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
    // Route::post('/staff/logout', [StaffAuthenticatedSessionController::class, 'destroy'])->name('staff.logout');
    Route::get('/customer/account', [CustomerAccountController::class, 'index'])->name('customer.account');
    Route::post('/customer/account/update', [CustomerAccountController::class, 'update'])->name('customer.account.update');

    // chat
    Route::get('/chat', [ChatRepsController::class, 'index'])->name('chat'); // List all SuperAdmins
    Route::get('/chat/{id}', [ChatRepsController::class, 'show'])->name('chat.show'); // Show specific chat
    Route::post('/chat/send', [ChatRepsController::class, 'store'])->name('chat.store'); // Send message

    Route::get('/customer/chat', [ChatRepsController::class, 'index'])->name('customer.chat.index');
    Route::get('/customer/chat/{superAdminId}', [ChatRepsController::class, 'show'])->name('customer.chat.show');
    Route::post('/customer/chat/store', [ChatRepsController::class, 'store'])->name('customer.chat.store');

    Route::get('/messages/new', [ChatRepsController::class, 'fetchNewMessages'])->name('customer.chat.newMessages');
    Route::get('/customer/chat/fetch-messages', [ChatRepsController::class, 'fetchNewMessages'])
    ->name('customer.chat.fetch');

    Route::get('/admin/chat/{id}', [ChatController::class, 'showChat'])->name('admin.chat');
    


});

// BREEZE AUTHERS
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });


//SuperAdmin Login Routes



//  Guest Routes for Super Admin Login



/// ✅ Guest Routes (Only accessible when logged out)
Route::middleware('guest:superadmin')->group(function () {
    Route::get('/superadmin/login', [SuperAdminAuthenticatedSessionController::class, 'create'])
        ->name('superadmin.login');
    
    Route::post('/superadmin/login', [SuperAdminAuthenticatedSessionController::class, 'store'])
        ->name('superadmin.login.store');
});

Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])
        ->name('admin.login');
    
    Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store'])
        ->name('admin.login.store');
});

Route::middleware('guest:staff')->group(function () {
    Route::get('/staff/login', [StaffAuthenticatedSessionController::class, 'create'])
        ->name('staff.login');
    
    Route::post('/staff/login', [StaffAuthenticatedSessionController::class, 'store'])
        ->name('staff.login.store');
});

// ✅ Unified Dashboard for Super Admin, Admin, and Staff
Route::middleware(['auth:superadmin,admin,staff'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // ✅ Shared dashboard view
    })->name('admin.dashboard');

  Route::post('user/logout', function (Request $request) {
    if (Auth::guard('superadmin')->check()) {
        Auth::guard('superadmin')->logout();
        return redirect()->route('superadmin.login')->with('status', 'Logged out successfully.');
    } elseif (Auth::guard('admin')->check()) {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('status', 'Logged out successfully.');
    } elseif (Auth::guard('staff')->check()) {
        Auth::guard('staff')->logout();
        return redirect()->route('staff.login')->with('status', 'Logged out successfully.');
    }

    return redirect('/login');
})->name('user.logout');

});
// ✅ Super Admin Routes
Route::middleware('auth:superadmin,admin')->group(function () {
    Route::get('/manageaccounts', [SuperAdminAccountController::class, 'index'])->name('superadmin.account.index');
    Route::post('/manageaccounts', [SuperAdminAccountController::class, 'store'])->name('superadmin.account.store');

    Route::get('/manageaccounts/{role}/{id}/edit', [SuperAdminAccountController::class, 'edit'])->name('superadmin.account.edit');
    Route::post('/manageaccounts/{role}/{id}/update', [SuperAdminAccountController::class, 'update'])->name('superadmin.account.update');

    Route::delete('/manageaccounts/{role}/{id}/delete', [SuperAdminAccountController::class, 'destroy'])->name('superadmin.account.delete');
});


Route::get('/2fa', [TwoFactorAuthController::class, 'index'])->name('2fa.verify');
Route::post('/2fa', [TwoFactorAuthController::class, 'verify'])->name('2fa.check');

Route::get('/2fa/resend', [TwoFactorAuthController::class, 'resend'])->name('2fa.resend');


Route::middleware(['auth:superadmin,admin,staff'])->group(function () {
    // Route::get('admin/chat', [MessageController::class, 'chat'])->name('admin.chat'); // Chat page
    
    
    Route::post('/chat/store', [ChatController::class, 'store'])->name('admin.chat.store');

    // Group Chat
    Route::get('/admin/group-chat', [GroupChatController::class, 'index'])->name('admin.group.chat');
    Route::post('/admin/group-chat/store', [GroupChatController::class, 'store'])->name('admin.group.chat.store');
    Route::get('/admin/chat/{user}', [ChatController::class, 'index'])->name('admin.chat.index');
    Route::post('/admin/chat/store', [ChatController::class, 'store'])->name('admin.chat.store');

    // admins and staff chats
    Route::get('admin/chat', [ChatController::class, 'showChat'])->name('admin.chat');
    Route::get('admin/chat/{id}', [ChatController::class, 'chatWithUser'])->name('admin.chatting');
    Route::get('/admin/chat/refresh', [ChatController::class, 'refresh'])->name('admin.chat.refresh');
    // Route::post('admin/chat/send', [ChattingController::class, 'sendMessage'])->name('send.message');
    // Route::post('/admin/chat/send', [ChattingController::class, 'storeMessage'])->name('admin.chat.send');
    // Route::get('/admin/chat/{id}', [ChattingController::class, 'chatWithUser'])->name('admin.chat');
    // Route::post('/admin/chat/send', [ChattingController::class, 'storeMessage'])->name('admin.chat.store');
    // Route::post('/chat/send', [ChattingController::class, 'storeMessage'])->name('chat.send');
});

// ✅ Keep Laravel Auth Routes
require __DIR__.'/auth.php';
