<?php

use Illuminate\Support\Facades\Route;

// Admin Controller
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\ProductlistingController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\ManageaccountController;


// Staff Controller
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\InventoryController as StaffInventoryController;
use App\Http\Controllers\Staff\OrderController as StaffOrderController;
use App\Http\Controllers\Staff\ChatController as StaffChatController;
use App\Http\Controllers\Staff\HistoryController as StaffHistoryController;
use App\Http\Controllers\Staff\LoginController as StaffLoginController;

// Customer Controller
use App\Http\Controllers\Customer\OrderController as CustomerOrderController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Customer\ManageorderController;
use App\Http\Controllers\Customer\ManageaccountController as CustomerManageaccountController;
use App\Http\Controllers\Customer\HistoryController as CustomerHistoryController;
use App\Http\Controllers\Customer\CustomerloginController;





//Super Admin Login
use App\Http\Controllers\Auth\SuperAdminAuthenticatedSessionController;
use App\Http\Controllers\SuperAdminDashboardController;


// ADMIN ROUTES
Route::get('admin/dashboard', [DashboardController::class, 'showDashboard'])->name('admin.dashboard');

Route::get('admin/inventory', [InventoryController::class, 'showInventory'])->name('admin.inventory');
Route::post('admin/inventory', [InventoryController::class, 'addStock'])->name('admin.inventory.store');


Route::get('admin/order', [OrderController::class, 'showOrder'])->name('admin.order');
Route::get('admin/chat', [ChatController::class, 'showChat'])->name('admin.chat');
Route::get('admin', [LoginController::class, 'showIndex'])->name('admin.index');
Route::get('admin/history', [HistoryController::class, 'showHistory'])->name('admin.history');

Route::get('admin/productlisting', [ProductlistingController::class, 'showProductListingPage'])->name('admin.productlisting');
Route::post('admin/productlisting/register/product', [ProductlistingController::class, 'registerNewProduct'])->name('admin.register.product');

Route::get('admin/manageaccount', [ManageaccountController::class, 'showManageaccount'])->name('admin.manageaccount');


// STAFF ROUTES
Route::get('staff/dashboard', [StaffDashboardController::class, 'showDashboard'])->name('staff.dashboard');
Route::get('staff/inventory', [StaffInventoryController::class, 'showInventory'])->name('staff.inventory');
Route::get('staff/order', [StaffOrderController::class, 'showOrder'])->name('staff.order');
Route::get('staff/chat', [StaffChatController::class, 'showChat'])->name('staff.chat');
Route::get('staff/history', [StaffHistoryController::class, 'showHistory'])->name('staff.history');
Route::get('staff/', [StaffLoginController::class, 'showLogin'])->name('staff.index');

// Customer
Route::get('customer/order', [CustomerOrderController::class, 'showOrder'])->name('customer.order');
Route::get('customer/chat', [CustomerChatController::class, 'showChat'])->name('customer.chat');
Route::get('customer/manageorder', [ManageorderController::class, 'showManageOrder'])->name('customer.manageorder');
Route::get('customer/manageaccount', [CustomerManageaccountController::class, 'showAccount'])->name('customer.manageaccount');
Route::get('customer/history', [CustomerHistoryController::class, 'showHistory'])->name('customer.history');
Route::get('customer/', [CustomerloginController::class, 'showLogin'])->name('customer.index');


// BREEZE AUTHERS
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });


//SuperAdmin Login Routes



//  Guest Routes for Super Admin Login



// ✅ Guest Routes (Only accessible when logged out)
Route::middleware('guest:superadmin')->group(function () {
    Route::get('/superadmin/login', [SuperAdminAuthenticatedSessionController::class, 'create'])
        ->name('superadmin.login');
    
    Route::post('/superadmin/login', [SuperAdminAuthenticatedSessionController::class, 'store'])
        ->name('superadmin.login.store'); // ✅ Corrected form action
});

// ✅ Authenticated Routes (Only accessible when logged in)
Route::middleware('auth:superadmin')->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'index'])
        ->name('superadmin.dashboard'); // ✅ Corrected redirect name

    Route::post('/superadmin/logout', [SuperAdminAuthenticatedSessionController::class, 'destroy'])
        ->name('superadmin.logout');
});



require __DIR__.'/auth.php';
