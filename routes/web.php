<?php

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// Admin Controller
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\ItemQrCodeController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\OcrInventoryController;
use App\Http\Controllers\Admin\HistoryController;

// Staff Controller
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\Admin\ChattingController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GroupChatController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\StaffLocationController;

// Customer Controller
use App\Http\Controllers\Admin\HistorylogController;

//Super Admin Login
use App\Http\Controllers\Customer\ChatRepsController;
use App\Http\Controllers\SuperAdminAccountController;
use App\Http\Controllers\Auth\TwoFactorAuthController;
use App\Http\Controllers\Admin\ManageaccountController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\Admin\ProductlistingController;




use App\Http\Controllers\Customer\ManageorderController;

// chat
use App\Http\Controllers\Customer\CustomerloginController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\StaffAuthenticatedSessionController;

// GroupChat
use App\Http\Controllers\Staff\ChatController as StaffChatController;




use App\Http\Controllers\Auth\SuperAdminAuthenticatedSessionController;
use App\Http\Controllers\Staff\LoginController as StaffLoginController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Staff\HistoryController as StaffHistoryController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;

use App\Http\Controllers\Staff\InventoryController as StaffInventoryController;
use App\Http\Controllers\Customer\HistoryController as CustomerHistoryController;
use App\Http\Controllers\Customer\ManageaccountController as CustomerManageaccountController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\PromotionalPageController;


//////super admin archive route
Route::middleware(['auth:superadmin,admin,staff'])->group(function () {
Route::post('/superadmin/accounts/{role}/{id}/archive', [SuperAdminAccountController::class, 'destroy'])->name('superadmin.account.archive');
Route::post('/superadmin/accounts/{role}/{id}/restore', [SuperAdminAccountController::class, 'restore'])->name('superadmin.account.restore');

});

Route::middleware(['auth:superadmin,admin,staff'])->group(function () {
Route::get('admin/historylog', [HistorylogController::class, 'showHistorylog'])->name('admin.historylog');
});
// ADMIN ROUTES
Route::middleware(['auth:superadmin,admin,staff'])->group(function () {

    //!!~~~~~~~~~~~~~~~~~~~~~~~~~ << ASSIGNED SUPERADMIN/ADMIN ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~!!//
    Route::middleware(['auth:superadmin,admin'])->group(function () {        
        //1///////////////////////// << INVENTORY ROUTES >> //////////////////////////////1//
        Route::get('admin/inventory/{location_filter?}', [InventoryController::class, 'showInventory'])->name('admin.inventory');
        Route::post('admin/inventory/', [InventoryController::class, 'showInventoryLocation'])->name('admin.inventory.location');
    
        Route::post('admin/inventory/register/product', [InventoryController::class, 'registerNewProduct'])->name('admin.register.product');
        Route::delete('admin/inventory/delete/product/{product}', [InventoryController::class, 'destroyProduct'])->name('admin.destroy.product');
    
        Route::post('admin/inventory/{addType}', [InventoryController::class, 'addStock'])->name('admin.inventory.store');
        Route::post('admin/inventory/search/{type}', [InventoryController::class, 'searchInventory'])->name('admin.inventory.search');

        Route::get('admin/inventory/export/{exportType}', [ExportController::class, 'export'])->name('admin.inventory.export');
        Route::post('admin/inventory/export/{exportType}', [ExportController::class, 'export'])->name('admin.inventory.export');

        // Route::get('/deducted-quantities/{year}/{month}', [InventoryController::class, 'getFilteredDeductedQuantities']);
        // Route::get('/inventory-by-month/{year}/{month}', [InventoryController::class, 'getInventoryByMonth']);
        Route::get('/inventory-by-month/{year}/{month}/{location?}', [DashboardController::class, 'getInventoryByMonth']);
Route::get('/deducted-quantities/{year}/{month}/{location?}', [DashboardController::class, 'getFilteredDeductedQuantities']);

        Route::get('/admin/dashboard', [DashboardController::class, 'showDashboard'])->name('admin.dashboard');
        //1///////////////////////// << INVENTORY ROUTES >> //////////////////////////////1//


        //2///////////////////////// << ORDER HISTORY ROUTE >> //////////////////////////////2//
        Route::get('admin/history', [HistoryController::class, 'showHistory'])->name('admin.history');
        //2//////////////////////// << ORDER HISTORY ROUTE >> //////////////////////////////2//


        //3///////////////////////// << PRODUCT DEALS ROUTES >> //////////////////////////////3//        
        Route::get('admin/productlisting/', [ProductlistingController::class, 'showProductListingPage'])->name('admin.productlisting');
        Route::post('admin/productlisting', [ProductlistingController::class, 'createExclusiveDeal'])->name('admin.productlisting.create');
        Route::put('admin/productlisting/{aidee}', [ProductlistingController::class, 'updateExclusiveDeal'])->name('admin.productlisting.update');
        Route::delete('admin/productlisting/{deal_id}/{company}', [ProductlistingController::class, 'destroyExclusiveDeal'])->name('admin.productlisting.destroy');
        //3///////////////////////// << PRODUCT DEALS ROUTES >> //////////////////////////////3//        


        //4///////////////////////// << ACCOUNT MANAGEMENT ROUTES >> //////////////////////////////4//        
        Route::get('/manageaccounts', [SuperAdminAccountController::class, 'index'])->name('superadmin.account.index');
        Route::post('/manageaccounts', [SuperAdminAccountController::class, 'store'])->name('superadmin.account.store');

        Route::get('/manageaccounts/{role}/{id}/edit', [SuperAdminAccountController::class, 'edit'])->name('superadmin.account.edit');
        Route::post('/manageaccounts/{role}/{id}/update', [SuperAdminAccountController::class, 'update'])->name('superadmin.account.update');

        Route::delete('/manageaccounts/{role}/{id}/delete', [SuperAdminAccountController::class, 'destroy'])->name('superadmin.account.delete');
        //4///////////////////////// << ACCOUNT MANAGEMENT ROUTES >> //////////////////////////////4// 


        //5///////////////////////// << QR CODE ROUTES >> //////////////////////////////5//
        
        // Route::get('/orders/{order}/generate-qr-code', [QRCodeController::class, 'generateOrderQrCode'])
        //     ->name('orders.generateQrCode');

        // Route::get('/orders/{order}/show-qr-code', [QrCodeController::class, 'showOrderQrCode'])
        //     ->name('orders.showQrCode');

        // Route::get('/scan-qr', function () {
        //     return view('orders.scan'); // Blade file for scanning QR codes
        // })->name('orders.scan');

        Route::get('/upload-qr', function () {
            return view('orders.upload_qr'); // Blade file for uploading QR codes
        })->name('upload.qr');

        Route::post('/upload-qr-code', [InventoryController::class, 'uploadQrCode'])->name('upload.qr.code');
        //5///////////////////////// << QR CODE ROUTES >> //////////////////////////////5//


        //5.5///////////////////////// << OCR ROUTES >> //////////////////////////////5.5//
        Route::get('/upload-receipt', function () {
            return view('upload_receipt');
        })->name('upload.receipt');
        
        Route::post('/process-receipt', [OcrInventoryController::class, 'uploadReceipt'])->name('process.receipt');
        Route::post('/save-receipt', [OcrInventoryController::class, 'saveInventory'])->name('save.receipt'); 
        Route::get('/get-locations', function () {
            $locations = Location::pluck('province')->toArray();
            return response()->json(['locations' => $locations]);
        })->name('get.locations');

        Route::put('/admin/inventory/transfer', [InventoryController::class, 'transferInventory'])->name('admin.inventory.transfer');

        //5.5///////////////////////// << OCR ROUTES >> //////////////////////////////5.5//

        //6.6///////////////////////// << HISTORY LOG ROUTES >> //////////////////////////////6.6//
        Route::get('admin/historylog', [HistorylogController::class, 'showHistorylog'])->name('admin.historylog');
    });
    //!!~~~~~~~~~~~~~~~~~~~~~~~~~ << ASSIGNED SUPERADMIN/ADMIN ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~!!//



    //??~~~~~~~~~~~~~~~~~~~~~~~~~ << ASSIGNED ROUTES FOR ALL EMPLOYEES >> ~~~~~~~~~~~~~~~~~~~~~~~~~~~~??//
    

    //6///////////////////////// << DASHBOARD ROUTE >> //////////////////////////////6//
    // Bakit dalawa yung dashboard routes dito??? <<<<<-------------------
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard'); // ✅ Shared dashboard view
    })->name('admin.dashboard');

    Route::get('/admin/dashboard', [DashboardController::class, 'showDashboard'])->name('admin.dashboard');
    //6///////////////////////// << DASHBOARD ROUTE >> //////////////////////////////6//


    //23///////////////////////// << QR CODE ROUTES >> //////////////////////////////23//
    Route::get('/orders/{order}/show-qr-code', [QrCodeController::class, 'showOrderQrCode'])
    ->name('orders.showQrCode');

    Route::get('/scan-qr', function () {
        return view('orders.scan'); // Blade file for scanning QR codes
    })->name('orders.scan');

    Route::post('/deduct-inventory', [InventoryController::class, 'deductInventory']);
    //23///////////////////////// << QR CODE ROUTES >> //////////////////////////////23//


    //69///////////////////////// << ACCOUNT ARHIVAL ROUTES >> //////////////////////////////69//
    Route::post('/superadmin/accounts/{role}/{id}/archive', [SuperAdminAccountController::class, 'destroy'])->name('superadmin.account.archive');
    Route::post('/superadmin/accounts/{role}/{id}/restore', [SuperAdminAccountController::class, 'restore'])->name('superadmin.account.restore');

    //69///////////////////////// << ACCOUNT ARHIVAL ROUTES >> //////////////////////////////69//


    //420///////////////////////// << HISTORY LOG ROUTES >> //////////////////////////////420//
    Route::get('admin/historylog', [HistorylogController::class, 'showHistorylog'])->name('admin.historylog');
    //420///////////////////////// << HISTORY LOG ROUTES >> //////////////////////////////420//


    //7///////////////////////// << EMPLOYEE LOGOUT ROUTES >> //////////////////////////////7//
    Route::post('user/logout', function (Request $request) {
        if (Auth::guard('superadmin')->check()) {
            Auth::guard('superadmin')->logout();
            return redirect()->route('superadmins.login')->with('status', 'Logged out successfully.');
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
            return redirect()->route('admins.login')->with('status', 'Logged out successfully.');
        } elseif (Auth::guard('staff')->check()) {
            Auth::guard('staff')->logout();
            return redirect()->route('staffs.login')->with('status', 'Logged out successfully.');
        }

        return redirect('/login');
    })->name('user.logout');
    //7///////////////////////// << EMPLOYEE LOGOUT ROUTES >> //////////////////////////////7//


    //8///////////////////////// << CURRENT ORDER ROUTES >> //////////////////////////////8//
    Route::get('admin/order', [OrderController::class, 'showOrder'])->name('admin.order');
    Route::put('admin/orders/{order}', [OrderController::class, 'updateOrder'])->name('admin.order.update');
    //8///////////////////////// << CURRENT ORDER ROUTES >> //////////////////////////////8//


    //9///////////////////////// << EMPLOYEE CHAT ROUTES >> //////////////////////////////9//
    // Group Chat
    Route::get('/admin/group-chat', [GroupChatController::class, 'index'])->name('admin.group.chat');
    Route::post('/admin/group-chat/store', [GroupChatController::class, 'store'])->name('admin.group.chat.store');
    // Route::get('/admin/chat/{user}', [ChatController::class, 'index'])->name('admin.chat.index');
    Route::post('/admin/chat/store', [ChatController::class, 'store'])->name('admin.chat.store');

    // admins and staff chats
    Route::get('admin/chat', [ChatController::class, 'showChat'])->name('employee.chat');
    Route::get('admin/chat/{id}', [ChatController::class, 'chatWithUser'])->name('admin.chatting');
    Route::get('/admin/chat/refresh', [ChatController::class, 'refresh'])->name('admin.chat.refresh');

    Route::get('/admin/get-latest-message', [ChatController::class, 'getLatestMessage'])->name('admin.getLatestMessage');
    Route::get('/admin/fetch-messages', [ChatController::class, 'fetchMessages'])->name('admin.fetchMessages');
    //9///////////////////////// << EMPLOYEE CHAT ROUTES >> //////////////////////////////9//


    //??~~~~~~~~~~~~~~~~~~~~~~~~ << ASSIGNED ROUTES FOR ALL EMPLOYEES >> ~~~~~~~~~~~~~~~~~~~~~~~~~~~~??//
});


//**~~~~~~~~~~~~~~~~~~~~~~~~~~~~ << ANYONE CAN ACCESS ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~~~~**//


//10///////////////////////// << 2FA ROUTES >> //////////////////////////////10//
Route::get('/2fa', [TwoFactorAuthController::class, 'index'])->name('2fa.verify');
Route::post('/2fa', [TwoFactorAuthController::class, 'verify'])->name('2fa.check');

Route::post('/2fa/resend', [TwoFactorAuthController::class, 'resend'])->name('2fa.resend');
Route::post('/two-factor/send-sms', [TwoFactorAuthController::class, 'sendViaSms'])->name('two-factor.sms');

//10///////////////////////// << 2FA ROUTES >> //////////////////////////////10//

/////////////////////////// << Promotional Page >> ////////////////////////////////
Route::get('/promotionalpage', [PromotionalPageController::class, 'showPromotionalPage'])->name('index');
/////////////////////////// << Promotional Page >> ////////////////////////////////


//**~~~~~~~~~~~~~~~~~~~~~~~~~~~~ << ANYONE CAN ACCESS ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~~~~**//


//##~~~~~~~~~~~~~~~~~~~~~~~~~ << AUTHENTICATED USERS ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~##//

Route::middleware(['auth', 'verified'])->group(function () {
    //11///////////////////////// << CUSTOMER ORDER ROUTES >> //////////////////////////////11//
    Route::get('customer/dashboard', [CustomerDashboardController::class, 'showDashboard'])->name('customer.dashboard');
    Route::get('customer/order', [CustomerOrderController::class, 'showOrder'])->name('customer.order');
    Route::post('customer/order', [CustomerOrderController::class, 'storeOrder'])->name('customer.order.store');
    //11///////////////////////// << CUSTOMER ORDER ROUTES >> //////////////////////////////11//

    
    //12////////////////////// << CUSTOMER MANAGE ORDER ROUTES >> ///////////////////////////12//
    Route::get('customer/manageorder', [ManageorderController::class, 'showManageOrder'])->name('customer.manageorder');
    //12////////////////////// << CUSTOMER MANAGE ORDER ROUTES >> ///////////////////////////12//
    

    //13////////////////////// << CUSTOMER ORDER HISTORY ROUTES >> ///////////////////////////13//
    Route::get('customer/history', [CustomerHistoryController::class, 'showHistory'])->name('customer.history');
    //13////////////////////// << CUSTOMER ORDER HISTORY ROUTES >> ///////////////////////////13//


    //14////////////////////// << CUSTOMER ACCOUNT MANAGEMENT ROUTES >> ///////////////////////////14//
    Route::get('customer/manageaccount', [CustomerManageaccountController::class, 'showAccount'])->name('customer.manageaccount');

    Route::get('/customer/account', [CustomerAccountController::class, 'index'])->name('customer.account');
    Route::post('/customer/account/update', [CustomerAccountController::class, 'update'])->name('customer.account.update');
    //14////////////////////// << CUSTOMER ACCOUNT MANAGEMENT ROUTES >> ///////////////////////////14//

    Route::post('/chat/mark-as-read', [ChatRepsController::class, 'markAsRead'])
    ->name('customer.chat.markAsRead');

});
    // //15///////////////////////// << CUSTOMER CHAT ROUTES >> //////////////////////////////15//
    // Route::get('/chat', [ChatRepsController::class, 'index'])->name('chat'); // List all SuperAdmins
    // Route::get('/chat/{id}', [ChatRepsController::class, 'show'])->name('chat.show'); // Show specific chat
    // Route::post('/chat/send', [ChatRepsController::class, 'store'])->name('chat.store'); // Send message

    // Route::get('/customer/chat', [ChatRepsController::class, 'index'])->name('customer.chat.index');
    // Route::get('/customer/chat/{superAdminId}', [ChatRepsController::class, 'show'])->name('customer.chat.show');
    // Route::post('/customer/chat/store', [ChatRepsController::class, 'store'])->name('customer.chat.store');

    // Route::get('/messages/new', [ChatRepsController::class, 'fetchNewMessages'])->name('customer.chat.newMessages');
    // Route::get('/customer/chat/fetch-messages', [ChatRepsController::class, 'fetchNewMessages'])
    // ->name('customer.chat.fetch');

    // Route::get('/customer/chat/{id}', [ChatController::class, 'showChat'])->name('admin.chat');
    //15///////////////////////// << CUSTOMER CHAT ROUTES >> //////////////////////////////15//

// ========================= CUSTOMER CHAT ROUTES ========================= //
Route::prefix('customer/chat')->middleware('auth')->group(function () {
    Route::get('/', [ChatRepsController::class, 'index'])->name('customer.chat.index'); // List SuperAdmins, Admins, and Staff to chat with
    Route::get('/{id}/{type}', [ChatRepsController::class, 'show'])->name('customer.chat.show'); // Open chat
    Route::post('/store', [ChatRepsController::class, 'store'])->name('customer.chat.store'); // Send message
    Route::get('/fetch-messages', [ChatRepsController::class, 'fetchNewMessages'])->name('customer.chat.fetch'); // Fetch new messages dynamically
    Route::post('/chat/mark-as-read', [ChatRepsController::class, 'markAsRead'])
    ->name('customer.chat.markAsRead');
});

// ========================= ADMIN, STAFF, SUPERADMIN CHAT ROUTES ========================= //
Route::prefix('admin/chat')->middleware('auth:admin,superadmin,staff')->group(function () {
    Route::get('/', [ChatController::class, 'showChat'])->name('admin.chat.index'); // List available chats for Admins, Staff, and SuperAdmins
    Route::get('/{id}/{type}', [ChatController::class, 'chatWithUser'])->name('admin.chat.show'); // Open chat
    Route::post('/send', [ChatController::class, 'store'])->name('admin.chat.store'); // Send message
    Route::get('/fetch-messages', [ChatController::class, 'fetchNewMessages'])->name('admin.chat.fetch'); // Fetch new messages dynamically
});

// ========================= COMMON CHAT ROUTES ========================= //
Route::prefix('chat')->middleware('auth')->group(function () {
    Route::get('/messages/new', [ChatRepsController::class, 'fetchNewMessages'])->name('chat.newMessages'); // Fetch new messages for all chat types
});

//##~~~~~~~~~~~~~~~~~~~~~~~~~ << AUTHENTICATED USERS ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~##//


//++~~~~~~~~~~~~~~~~~~~~~~~~~ << GUEST USERS ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~~~++//

//16////////////////////// << SUPERADMIN LOGIN ROUTES >> ///////////////////////////16//
Route::middleware('guest:superadmin')->group(function () {
    Route::get('/superadmin/login', [SuperAdminAuthenticatedSessionController::class, 'create'])
        ->name('superadmins.login');
    
    Route::post('/superadmin/login', [SuperAdminAuthenticatedSessionController::class, 'store'])
        ->name('superadmin.login.store');
});
//16////////////////////// << SUPERADMIN LOGIN ROUTES >> ///////////////////////////16//


//17////////////////////// << ADMIN LOGIN ROUTES >> ///////////////////////////17//
Route::middleware('guest:admin')->group(function () {
    Route::get('/admin/login', [AdminAuthenticatedSessionController::class, 'create'])
        ->name('admins.login');
    
    Route::post('/admin/login', [AdminAuthenticatedSessionController::class, 'store'])
        ->name('admin.login.store');
});
//17////////////////////// << ADMIN LOGIN ROUTES >> ///////////////////////////17//

//18////////////////////// << STAFF LOGIN ROUTES >> ///////////////////////////18//
Route::middleware('guest:staff')->group(function () {
    Route::get('/staff/login', [StaffAuthenticatedSessionController::class, 'create'])
        ->name('staffs.login');
    
    Route::post('/staff/login', [StaffAuthenticatedSessionController::class, 'store'])
        ->name('staff.login.store');
});
//18////////////////////// << STAFF LOGIN ROUTES >> ///////////////////////////18//

//++~~~~~~~~~~~~~~~~~~~~~~~~~ << GUEST USERS ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~~~++//


Route::middleware('auth:staff')->group(function () {
    Route::post('/update-location', [StaffLocationController::class, 'updateLocation'])->name('api.update-location');
    Route::get('/staff-locations', [StaffLocationController::class, 'getLocations'])->name('api.staff-locations');


});

Route::middleware('auth:admin,superadmin')->group(function () {
    Route::get('/stafflocation', [StaffLocationController::class, 'index'])->name('admin.stafflocation');
    Route::get('/staff-locations', [StaffLocationController::class, 'getLocations'])->name('api.staff-locations');

});


// To Keep Laravel Auth Routes
require __DIR__.'/auth.php';
require __DIR__.'/api.php';

