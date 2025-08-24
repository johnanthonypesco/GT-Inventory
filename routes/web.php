<?php

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// Admin Controller
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\ItemQrCodeController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\FileDownloadController;
use App\Http\Controllers\OcrInventoryController;
use App\Http\Controllers\Admin\FileOcrController;
use App\Http\Controllers\Admin\HistoryController;

// Staff Controller
use App\Http\Controllers\Export\ExportController;
use App\Http\Controllers\ReviewManagerController;
use App\Http\Controllers\StaffLocationController;
use App\Http\Controllers\Admin\ChattingController;
use App\Http\Controllers\Admin\BlockedIpController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\GroupChatController;

// Customer Controller
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Customer\ReviewController;


//Super Admin Login
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\PromotionalPageController;
use App\Http\Controllers\Admin\HistorylogController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Customer\ChatRepsController;
use App\Http\Controllers\Customer\TrackingController;
// use app\http\Controllers\ExportController as ExportDocxController;
use App\Http\Controllers\SuperAdminAccountController;


use App\Http\Controllers\Auth\TwoFactorAuthController;


use App\Http\Controllers\ProductSeasonalityController;

// chat
use App\Http\Controllers\Admin\ManageaccountController;
use App\Http\Controllers\SuperAdminDashboardController;




use App\Http\Controllers\Admin\ProductlistingController;
use App\Http\Controllers\Customer\ManageorderController;

// GroupChat
use App\Http\Controllers\Admin\AccountSecurityController;




use App\Http\Controllers\Customer\CustomerloginController;
use App\Http\Controllers\Admin\ContentmanagementController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\StaffAuthenticatedSessionController;
use App\Http\Controllers\ExportController as ExportDocxController;
use App\Http\Controllers\Staff\ChatController as StaffChatController;
use App\Http\Controllers\Auth\SuperAdminAuthenticatedSessionController;

use App\Http\Controllers\Staff\LoginController as StaffLoginController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Staff\HistoryController as StaffHistoryController;
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
// download apk for registration.
use App\Http\Controllers\Staff\InventoryController as StaffInventoryController;
use App\Http\Controllers\Customer\HistoryController as CustomerHistoryController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\ManageaccountController as CustomerManageaccountController;

// Route::get('/beta-register', [BetaRegistrationController::class, 'showForm'])->name('beta.register.form');
// Route::post('/beta-register', [BetaRegistrationController::class, 'store'])->name('beta.register.store');
Route::get('loginpage', [SampleController::class, 'showlogin'])->name('loginpage');

// Existing Route for Staff/Admin App
Route::get('/download/app', [FileDownloadController::class, 'downloadApk'])->name('apk.download');

// ✅ ADDED: New route for the Customer App download
Route::get('/download/customer-app', [FileDownloadController::class, 'downloadCustomerApk'])->name('customer.apk.download');
//////super admin archive route
Route::middleware(['auth:superadmin,admin,staff'])->group(function () {
Route::post('/superadmin/accounts/{role}/{id}/archive', [SuperAdminAccountController::class, 'destroy'])->name('superadmin.account.archive');
Route::post('/superadmin/accounts/{role}/{id}/restore', [SuperAdminAccountController::class, 'restore'])->name('superadmin.account.restore');

});

Route::middleware(['auth:superadmin,admin,staff'])->group(function () {
Route::get('admin/historylog', [HistorylogController::class, 'showHistorylog'])->name('admin.historylog');
// In your ADMIN routes file
Route::get('/admin/orders/{order}/available-staff', [OrderController::class, 'getAvailableStaff'])->name('admin.order.available_staff');

});
// ADMIN ROUTES
Route::middleware(['auth:superadmin,admin,staff'])->group(function () {

    //!!~~~~~~~~~~~~~~~~~~~~~~~~~ << ASSIGNED SUPERADMIN/ADMIN ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~!!//
    Route::middleware(['auth:superadmin,admin'])->group(function () {
        Route::get('/admin/dashboard', [DashboardController::class, 'showDashboard'])->name('admin.dashboard');
        Route::post('/admin/filtered-revenue', [App\Http\Controllers\Admin\DashboardController::class, 'getFilteredTotalRevenue'])->name('admin.filtered-revenue');
        //1///////////////////////// << INVENTORY ROUTES >> //////////////////////////////1//
        Route::get('admin/inventory/', [InventoryController::class, 'showInventory'])->name('admin.inventory');
        Route::post('admin/inventory/', [InventoryController::class, 'showInventoryLocation'])->name('admin.inventory.location');

        Route::post('admin/inventory/register/product', [InventoryController::class, 'registerNewProduct'])->name('admin.register.product');
        Route::put('admin/inventory/product/', [InventoryController::class, 'editRegisteredProduct'])->name('admin.edit.product');
        Route::put('admin/inventory/archive/product/{product}/{type?}', [InventoryController::class, 'archiveProduct'])->name('admin.archive.product');

        Route::put('admin/inventory/', [InventoryController::class, 'editStock'])->name('admin.edit.stock');

        Route::post('admin/inventory/{addType}', [InventoryController::class, 'addStock'])->name('admin.inventory.store');
        Route::post('admin/inventory/search/{type}', [InventoryController::class, 'searchInventory'])->name('admin.inventory.search');

        Route::get('admin/inventory/export/{exportType}/{exportSpecification?}/{secondaryExportSpecification?}', [ExportController::class, 'export'])->name('admin.inventory.export');
        Route::post('admin/inventory/export/{exportType}/{exportSpecification?}/{secondaryExportSpecification?}', [ExportController::class, 'export'])->name('admin.inventory.export');
        // Route::post('/accounts/disable', [AccountSecurityController::class, 'disable'])->name('accounts.disable');
        // Route::post('/ips/block', [BlockedIpController::class, 'block'])->name('ips.block');
        // // dashboard routes
         // API routes for dashboard charts
        Route::get('admin/revenue-data/{period}/{year}/{month?}/{week?}', [DashboardController::class, 'getRevenueData']);
        Route::get('admin/filtered-deducted-quantities/{year}/{month}/{location?}', [DashboardController::class, 'getFilteredDeductedQuantities']);
        // Route::get('admin/inventory-levels/{year}/{month}/{locationId?}', [DashboardController::class, 'getInventoryLevels']);
        Route::get('admin/inventory-levels/{locationId?}', [DashboardController::class, 'getInventoryLevels']);
        Route::get('admin/trending-products', [DashboardController::class, 'getTrendingProducts']);

        // New API routes for added dashboard charts
        Route::get('admin/order-status-counts', [DashboardController::class, 'getOrderStatusCounts']);
        Route::get('admin/average-order-value/{year}/{month?}', [DashboardController::class, 'getAverageOrderValue']);
        Route::get('admin/fulfillment-time/{period}/{year}/{month?}', [DashboardController::class, 'getOrderFulfillmentTime']);
        Route::get('admin/orders-by-location/{year}/{month?}', [DashboardController::class, 'getOrdersByLocation']);

        // NEW: AI Chart Analysis Route
        // Route::post('admin/analyze-charts', [DashboardController::class, 'analyzeChartsWithAI'])->name('admin.analyze.charts');
        // Route::post('/admin/generate-ai-summary', [DashboardController::class, 'ajaxGenerateExecutiveSummary'])->name('admin.generate.ai.summary');
        Route::post('admin/ai-handler', [DashboardController::class, 'handleAiRequest'])->name('admin.ai.handler');
        Route::get('/revenue-data', [DashboardController::class, 'getRevenueData']);
        // for realtime
        Route::get('/dashboard-stats', [App\Http\Controllers\Admin\DashboardController::class, 'getDashboardStats'])->name('api.dashboard-stats');
        
        Route::post('/save-inventory', [OcrInventoryController::class, 'saveInventory'])->name('save.inventory');
        // sales reports
        Route::get('admin/sales', [SalesReportController::class, 'index'])->name('admin.sales');
        Route::post('admin/sales/generate', [SalesReportController::class, 'generateReport'])->name('admin.sales.generate');
        
        //1///////////////////////// << INVENTORY ROUTES >> //////////////////////////////1//


        //2///////////////////////// << ORDER HISTORY ROUTE >> //////////////////////////////2//
        Route::get('admin/history', [HistoryController::class, 'showHistory'])->name('admin.history');
        //2//////////////////////// << ORDER HISTORY ROUTE >> //////////////////////////////2//


        //3///////////////////////// << PRODUCT DEALS ROUTES >> //////////////////////////////3//
        Route::get('admin/productlisting/', [ProductlistingController::class, 'showProductListingPage'])->name('admin.productlisting');
        Route::post('admin/productlisting', [ProductlistingController::class, 'createExclusiveDeal'])->name('admin.productlisting.create');
        Route::put('admin/productlisting/{aidee}', [ProductlistingController::class, 'updateExclusiveDeal'])->name('admin.productlisting.update');
        Route::put('admin/productlisting/{deal_id}/{company}/{type?}', [ProductlistingController::class, 'archiveExclusiveDeal'])->name('admin.productlisting.archive');
        //3///////////////////////// << PRODUCT DEALS ROUTES >> //////////////////////////////3//


        //4///////////////////////// << ACCOUNT MANAGEMENT ROUTES >> //////////////////////////////4//
        Route::get('/manageaccounts', [SuperAdminAccountController::class, 'index'])->name('superadmin.account.index');
        Route::post('/manageaccounts', [SuperAdminAccountController::class, 'store'])->name('superadmin.account.store');

        Route::get('/manageaccounts/{role}/{id}/edit', [SuperAdminAccountController::class, 'edit'])->name('superadmin.account.edit');
        // Route::post('/manageaccounts/{role}/{id}/update', [SuperAdminAccountController::class, 'update'])->name('superadmin.account.update');
        Route::put('/manageaccounts/{role}/{id}/update', [SuperAdminAccountController::class, 'update'])->name('superadmin.account.update');

        Route::delete('/manageaccounts/{role}/{id}/delete', [SuperAdminAccountController::class, 'destroy'])->name('superadmin.account.delete');

        // Add these with your other superadmin routes
        Route::post('/manageaccounts/check-email', [SuperAdminAccountController::class, 'checkEmail'])->name('superadmin.account.checkEmail');
        Route::post('/manageaccounts/check-contact', [SuperAdminAccountController::class, 'checkContact'])->name('superadmin.account.checkContact');
        //4///////////////////////// << ACCOUNT MANAGEMENT ROUTES >> //////////////////////////////4//

        //5///////////////////////// << CONTENT MANAGEMENT ROUTES >> //////////////////////////////5//
        Route::get('admin/contentmanagement', [ContentmanagementController::class, 'showContentmanagement'])->name('admin.contentmanagement');

        Route::post('/admin/products/bulk-select', [ContentmanagementController::class, 'selectmultipleproduct'])->name('admin.contentmanagement.selectmultipleproduct');

        Route::put('/admin/contentmanagement/edit/{id}', [ContentmanagementController::class, 'editContent'])->name('admin.contentmanagement.edit');
        Route::put('/admin/product/{id}/enabledisable', [ContentmanagementController::class, 'enabledisable'])->name('admin.product.enabledisable');
        Route::get('/admin/ocr-files', [FileOcrController::class, 'index'])->name('admin.file-ocr.index');
        Route::get('/admin/ocr-files', [FileOcrController::class, 'index'])->name('admin.file-ocr.index');
        Route::get('/admin/ocr-files/contents', [FileOcrController::class, 'getFolderContents'])->name('admin.file-ocr.contents');


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
        Route::post('/api/check-product', [OcrInventoryController::class, 'checkProduct'])->name('product.check');
        Route::get('/get-locations', function () {
            $locations = Location::pluck('province')->toArray();
            return response()->json(['locations' => $locations]);
        })->name('get.locations');
        Route::post('/products/analyze-recent-sales', [ProductSeasonalityController::class, 'analyzeRecentSales'])->name('products.analyzeRecentSales');
        Route::put('/admin/inventory/transfer', [InventoryController::class, 'transferInventory'])->name('admin.inventory.transfer');

        //5.5///////////////////////// << OCR ROUTES >> //////////////////////////////5.5//
        // Route::post('/export-inventory', [ExportDocxController::class, 'exportDocx'])->name('inventory.export');
        //6.6///////////////////////// << HISTORY LOG ROUTES >> //////////////////////////////6.6//
        // Initial page load for the history log
        Route::get('admin/historylog', [HistorylogController::class, 'showHistorylog'])->name('admin.historylog.show');
        
        // AJAX route for filtering, searching, and paginating history logs
        Route::get('admin/historylog/search', [HistorylogController::class, 'searchHistorylog'])->name('admin.historylog.search');
    }); 
    //!!~~~~~~~~~~~~~~~~~~~~~~~~~ << ASSIGNED SUPERADMIN/ADMIN ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~!!//
    // nilabas koto dahil ewan koba ayaw pag nasa loob e
    Route::post('/export-inventory', [ExportDocxController::class, 'exportDocx'])->name('inventory.export');

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
    // ✅ ADD THIS NEW ROUTE FOR FETCHING MESSAGES
    Route::get('/admin/group-chat/messages', [GroupChatController::class, 'fetchMessages'])->name('admin.group.chat.fetch');
    // Route::get('/admin/chat/{user}', [ChatController::class, 'index'])->name('admin.chat.index');
    Route::post('/admin/chat/store', [ChatController::class, 'store'])->name('admin.chat.store');

    // admins and staff chats
    // Route::get('admin/chat', [ChatController::class, 'showChat'])->name('employee.chat');
    // Route::get('admin/chat/{id}', [ChatController::class, 'chatWithUser'])->name('admin.chatting');
    // Route::get('/admin/chat/refresh', [ChatController::class, 'refresh'])->name('admin.chat.refresh');

    // Route::get('/admin/get-latest-message', [ChatController::class, 'getLatestMessage'])->name('admin.getLatestMessage');
    // Route::get('/admin/fetch-messages', [ChatController::class, 'fetchMessages'])->name('admin.fetchMessages');
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
Route::get('/', [PromotionalPageController::class, 'showPromotionalPage'])->name('index');
/////////////////////////// << Promotional Page >> ////////////////////////////////

//**~~~~~~~~~~~~~~~~~~~~~~~~~~~~ << ANYONE CAN ACCESS ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~~~~**//


//##~~~~~~~~~~~~~~~~~~~~~~~~~ << AUTHENTICATED USERS ROUTES >> ~~~~~~~~~~~~~~~~~~~~~~~~~##//

Route::middleware(['auth', 'verified'])->group(function () {

/////order tracking////
 Route::get('/track-order/{order}/location', [TrackingController::class, 'getStaffLocationForOrder'])->name('track.location');
 
/////order tracking///

       //11///////////////////////// << CUSTOMER Review ROUTES >> //////////////////////////////11//
   Route::post('/review', [ReviewController::class, 'store'])->name('customer.review.store');

    //11///////////////////////// << CUSTOMER Review ROUTES >> //////////////////////////////11//


    
    //11///////////////////////// << CUSTOMER ORDER ROUTES >> //////////////////////////////11//
    Route::get('customer/dashboard', [CustomerDashboardController::class, 'showDashboard'])->name('customer.dashboard');
    Route::get('customer/dashboard', [CustomerDashboardController::class, 'showDashboard'])->name('customer.dashboard');
    Route::get('customer/order', [CustomerOrderController::class, 'showOrder'])->name('customer.order');
    Route::post('customer/order', [CustomerOrderController::class, 'storeOrder'])->name('customer.order.store');
    Route::post('customer/reorder-last', [CustomerOrderController::class, 'reorderLastPurchase'])->name('customer.order.reorderLast');

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

    // Route::post('/chat/mark-as-read', [ChatRepsController::class, 'markAsRead'])
    // ->name('customer.chat.markAsRead');

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
Route::middleware('auth', 'verified')->group(function () {
    Route::prefix('customer/chat')->name('customer.chat.')->group(function () {
        Route::get('/', [ChatRepsController::class, 'index'])->name('index');
        Route::get('/{id}/{type}', [ChatRepsController::class, 'show'])->name('show');
        Route::post('/store', [ChatRepsController::class, 'store'])->name('store');
        
        // ✅ ADD THIS NEW ROUTE FOR FETCHING
        Route::get('/{id}/{type}/fetch', [ChatRepsController::class, 'fetchNewMessages'])->name('fetchMessages');
        
        Route::post('/mark-as-read', [ChatRepsController::class, 'markAsRead'])->name('markAsRead');
    });
});

// ========================= ADMIN, STAFF, SUPERADMIN CHAT ROUTES ========================= //
Route::prefix('admin/chat')
    ->middleware('auth:admin,superadmin,staff') // Use your existing auth middleware
    ->name('admin.chat.')
    ->group(function () {
        Route::get('/', [ChatController::class, 'showChat'])->name('index');
        Route::get('/{id}/{type}', [ChatController::class, 'chatWithUser'])->name('show');
        Route::post('/send', [ChatController::class, 'store'])->name('store');
        
        // ✅ ADD THIS NEW ROUTE
        Route::get('/{id}/{type}/fetch', [ChatController::class, 'fetchNewMessages'])->name('fetchMessages');
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
      Route::get('/superadmin/reviews', [ReviewManagerController::class, 'index'])->name('superadmin.reviews.index');
    Route::post('/superadmin/reviews/{review}/approve', [ReviewManagerController::class, 'approve'])->name('superadmin.reviews.approve');
    Route::post('/superadmin/reviews/{review}/disapprove', [ReviewManagerController::class, 'disapprove'])->name('superadmin.reviews.disapprove');
      Route::get('/superadmin/reviews', [ReviewManagerController::class, 'index'])->name('superadmin.reviews.index');
    Route::post('/superadmin/reviews/{review}/approve', [ReviewManagerController::class, 'approve'])->name('superadmin.reviews.approve');
    Route::post('/superadmin/reviews/{review}/disapprove', [ReviewManagerController::class, 'disapprove'])->name('superadmin.reviews.disapprove');

});



Route::middleware(['auth:superadmin,admin'])->group(function () {
    // Route for updating company details
    Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('company.update');

    // Route for archiving (soft deleting) a company
    Route::delete('/companies/{company}', [CompanyController::class, 'destroy'])->name('company.destroy');

    // In routes/web.php
Route::post('/manage-companies/{id}/restore', [CompanyController::class, 'restore'])->name('admin.companies.restore');
});


// To Keep Laravel Auth Routes
require __DIR__.'/auth.php';
require __DIR__.'/api.php';

