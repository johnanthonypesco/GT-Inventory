<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController\DashboardController;
use App\Http\Controllers\AdminController\ProductMovementController;
use App\Http\Controllers\AdminController\InventoryController;
use App\Http\Controllers\AdminController\PatientRecordsController;
use App\Http\Controllers\AdminController\HistorylogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', [DashboardController::class, 'showdashboard'])->name('admin.dashboard');
Route::get('/admin/productmovement', [ProductMovementController::class, 'showproductmovement'])->name('admin.productmovement');
Route::get('/admin/inventory', [InventoryController::class, 'showinventory'])->name('admin.inventory');
Route::get('/admin/patientrecords', [PatientRecordsController::class, 'showpatientrecords'])->name('admin.patientrecords');
Route::get('/admin/historylog', [HistorylogController::class, 'showhistorylog'])->name('admin.historylog');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
