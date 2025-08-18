<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\PasswordResetMailController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\AdminAuthenticatedSessionController;
use App\Http\Controllers\Auth\StaffAuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\SuperAdminAuthenticatedSessionController;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');
    
    // Route::view('/', "index");


    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
    //     ->name('password.request');

    // Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
    //     ->name('password.email');

    // Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
    //     ->name('password.reset');

    // Route::post('reset-password', [NewPasswordController::class, 'store'])
    
    //     ->name('password.store');



   // Superadmin Routes
// Route::prefix('superadmin')->group(function () {
    // Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('superadmin.password.request');
    // Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('superadmins.password.email');
    // Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('superadmin.password.reset');
    // Route::post('reset-password', [NewPasswordController::class, 'store'])->name('superadmins.password.store');
});

// Admin Routes
// Route::prefix('admin')->group(function () {
//     Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('admins.password.request');
    // Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('admins.password.email');
    // Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('admin.password.reset');
    // Route::post('reset-password', [NewPasswordController::class, 'store'])->name('admins.password.store');

// Staff Routes
// Route::prefix('staff')->group(function () {
//     Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('staffs.password.request');
    // Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('staffs.password.email');
    // Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('staff.password.reset');
    // Route::post('reset-password', [NewPasswordController::class, 'store'])->name('staffs.password.store');

// Default User Routes
// Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('users.password.request');
// Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
// Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
// Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');


// Default User Routes
Route::middleware('guest')->group(function () {
    Route::get('forgot-password', function() {
        return view('auth.forgot-password', ['userType' => 'users']);
    })->name('users.password.request');
    Route::post('forgot-password', [PasswordResetMailController::class, 'sendResetLink'])->name('users.password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('users.password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('users.password.store');
});

// Admin Routes
Route::prefix('admin')->middleware('guest')->group(function () {
    Route::get('forgot-password', function() {
        return view('auth.forgot-password', ['userType' => 'admins']);
    })->name('admin.password.request');
    Route::post('forgot-password', [PasswordResetMailController::class, 'sendResetLink'])->name('admins.password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('admins.password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('admins.password.store');
});

// Staff Routes
Route::prefix('staff')->middleware('guest')->group(function () {
    Route::get('forgot-password', function() {
        return view('auth.forgot-password', ['userType' => 'staffs']);
    })->name('staff.password.request');
    Route::post('forgot-password', [PasswordResetMailController::class, 'sendResetLink'])->name('staffs.password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('staffs.password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('staffs.password.store');
});

// // Superadmin Routes
Route::prefix('superadmin')->middleware('guest')->group(function () {
    Route::get('forgot-password', function() {
        return view('auth.forgot-password', ['userType' => 'superadmins']);
    })->name('superadmin.password.request');
    Route::post('forgot-password', [PasswordResetMailController::class, 'sendResetLink'])->name('superadmins.password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('superadmins.password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('superadmins.password.store');
});


Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    // Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
    //     ->name('logout');
       
   


       
        
});


Route::middleware('auth:web')->group(function () {
    // ... your other user routes
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

// For Admins
Route::prefix('admin')->middleware('auth:admin')->group(function () {
    // ... your other admin routes
    Route::post('logout', [AdminAuthenticatedSessionController::class, 'destroy'])->name('admin.logout');
});

// For Staff
Route::prefix('staff')->middleware('auth:staff')->group(function () {
    // ... your other staff routes
    Route::post('logout', [StaffAuthenticatedSessionController::class, 'destroy'])->name('staff.logout');
});

// For Super Admins
Route::prefix('superadmin')->middleware('auth:superadmin')->group(function () {
    // ... your other superadmin routes
    Route::post('logout', [SuperAdminAuthenticatedSessionController::class, 'destroy'])->name('superadmin.logout');
});

Route::middleware('guest')->group(function () {
    Route::get('/beta-register', [\App\Http\Controllers\BetaRegistrationController::class, 'showForm'])->name('beta.register');
    Route::post('/beta-register', [\App\Http\Controllers\BetaRegistrationController::class, 'store'])->name('beta.register.store');
});
