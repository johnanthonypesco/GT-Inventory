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

// Admin
Route::get('admin/dashboard', [DashboardController::class, 'showDashboard'])->name('admin.dashboard');
Route::get('admin/inventory', [InventoryController::class, 'showInventory'])->name('admin.inventory');
Route::get('admin/order', [OrderController::class, 'showOrder'])->name('admin.order');
Route::get('admin/chat', [ChatController::class, 'showChat'])->name('admin.chat');
Route::get('admin', [LoginController::class, 'showIndex'])->name('admin.index');
Route::get('admin/history', [HistoryController::class, 'showHistory'])->name('admin.history');
Route::get('admin/productlisting', [ProductlistingController::class, 'showProductlisting'])->name('admin.productlisting');
Route::get('admin/manageaccount', [ManageaccountController::class, 'showManageaccount'])->name('admin.manageaccount');

// Staff
Route::get('staff/dashboard', [StaffDashboardController::class, 'showDashboard'])->name('staff.dashboard');
Route::get('staff/inventory', [StaffInventoryController::class, 'showInventory'])->name('staff.inventory');
Route::get('staff/order', [StaffOrderController::class, 'showOrder'])->name('staff.order');
Route::get('staff/chat', [StaffChatController::class, 'showChat'])->name('staff.chat');
Route::get('staff/history', [StaffHistoryController::class, 'showHistory'])->name('staff.history');
Route::get('staff/', [StaffLoginController::class, 'showLogin'])->name('staff.index');


// BREEZE AUTHERS
// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

require __DIR__.'/auth.php';
