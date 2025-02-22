<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Admin Controller
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\HistoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\SuperAdminAccountController;
use App\Http\Controllers\Admin\ManageaccountController;


// Staff Controller
use App\Http\Controllers\SuperAdminDashboardController;
use App\Http\Controllers\Admin\ProductlistingController;
use App\Http\Controllers\Customer\ManageorderController;
use App\Http\Controllers\Customer\CustomerloginController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\StaffAuthenticatedSessionController;

// Customer Controller
use App\Http\Controllers\Staff\ChatController as StaffChatController;


//Super Admin Login
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



// ADMIN ROUTES

Route::get('admin/inventory', [InventoryController::class, 'showInventory'])->name('admin.inventory');
Route::post('admin/inventory', [InventoryController::class, 'addStock'])->name('admin.inventory.store');
Route::post('admin/inventory/search', [InventoryController::class, 'searchInventory'])->name('admin.inventory.search');


Route::get('admin/order', [OrderController::class, 'showOrder'])->name('admin.order');
Route::get('admin/chat', [ChatController::class, 'showChat'])->name('admin.chat');
Route::get('admin', [LoginController::class, 'showIndex'])->name('admin.index');
Route::get('admin/history', [HistoryController::class, 'showHistory'])->name('admin.history');

Route::get('admin/productlisting', [ProductlistingController::class, 'showProductListingPage'])->name('admin.productlisting');
Route::post('admin/inventory/register/product', [InventoryController::class, 'registerNewProduct'])->name('admin.register.product');
Route::delete('admin/inventory/delete/product/{product}', [InventoryController::class, 'destroyProduct'])->name('admin.destroy.product');

Route::get('admin/manageaccount', [ManageaccountController::class, 'showManageaccount'])->name('admin.manageaccount');


// STAFF ROUTES
Route::get('staff/dashboard', [StaffDashboardController::class, 'showDashboard'])->name('staff.dashboard');
Route::get('staff/inventory', [StaffInventoryController::class, 'showInventory'])->name('staff.inventory');
Route::get('staff/order', [StaffOrderController::class, 'showOrder'])->name('staff.order');
Route::get('staff/chat', [StaffChatController::class, 'showChat'])->name('staff.chat');
Route::get('staff/history', [StaffHistoryController::class, 'showHistory'])->name('staff.history');
Route::get('staff/', [StaffLoginController::class, 'showLogin'])->name('staff.index');

Route::middleware(['auth', 'verified'])->group(function () {
Route::get('customer/order', [CustomerOrderController::class, 'showOrder'])->name('customer.order');
Route::get('customer/chat', [CustomerChatController::class, 'showChat'])->name('customer.chat');
Route::get('customer/manageorder', [ManageorderController::class, 'showManageOrder'])->name('customer.manageorder');
Route::get('customer/manageaccount', [CustomerManageaccountController::class, 'showAccount'])->name('customer.manageaccount');
Route::get('customer/history', [CustomerHistoryController::class, 'showHistory'])->name('customer.history');
Route::get('customer/', [CustomerloginController::class, 'showLogin'])->name('customer.index');
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

    Route::post('/logout', function (Request $request) {
        if (Auth::guard('superadmin')->check()) {
            Auth::guard('superadmin')->logout();
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('staff')->check()) {
            Auth::guard('staff')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login'); // ✅ Redirect to generic login page
    })->name('logout');
});

// ✅ Super Admin Routes
Route::middleware('auth:superadmin,admin')->group(function () {
    Route::get('/manageaccounts', [SuperAdminAccountController::class, 'index'])->name('superadmin.account.index');
    Route::post('/manageaccounts', [SuperAdminAccountController::class, 'store'])->name('superadmin.account.store');

    Route::get('/manageaccounts/{role}/{id}/edit', [SuperAdminAccountController::class, 'edit'])->name('superadmin.account.edit');
    Route::post('/manageaccounts/{role}/{id}/update', [SuperAdminAccountController::class, 'update'])->name('superadmin.account.update');

    Route::delete('/manageaccounts/{role}/{id}/delete', [SuperAdminAccountController::class, 'destroy'])->name('superadmin.account.delete');
});

// ✅ Keep Laravel Auth Routes
require __DIR__.'/auth.php';
