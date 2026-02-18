<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\Organization\RegisterController as OrganizationRegisterController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('home');

Route::middleware('guest')->group(function (): void {
    Route::as('auth.')->group(function (): void {
        Route::get('register', [RegisterController::class, 'register'])->name('register');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        Route::get('register/organization', [OrganizationRegisterController::class, 'create'])->name('register.organization');
        Route::post('register/organization', [OrganizationRegisterController::class, 'store'])->name('register.organization.store')->middleware([HandlePrecognitiveRequests::class]);
        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');

        Route::get('forgot-password', [PasswordResetController::class, 'showPasswordResetRequestForm'])->name('password.request');
        Route::post('forgot-password', [PasswordResetController::class, 'sendPasswordResetEmail'])->name('password.email')->middleware('throttle:password-reset-request');
        Route::get('reset-password/{token}', [PasswordResetController::class, 'showPasswordResetForm'])->name('password.reset');
        Route::post('reset-password/update', [PasswordResetController::class, 'resetPassword'])->name('password.store')->middleware('throttle:password-reset');
    });
});

Route::middleware('auth')->group(function (): void {
    Route::as('auth.')->group(function (): void {
        Route::post('logout', LogoutController::class)->name('logout');
        Route::get('email/verify', [EmailVerificationController::class, 'index'])->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed', 'throttle:10,1'])->name('verification.verify');
        Route::post('email/verification-notification', [EmailVerificationController::class, 'resend'])->middleware('throttle:5,1')->name('verification.send');
    });
    Route::middleware(['can:view-dashboard', 'verified:auth.verification.notice'])->as('dashboard.')->group(function (): void {
        Route::get('dashboard', DashboardController::class)->name('index');
    });
});
