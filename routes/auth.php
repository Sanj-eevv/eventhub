<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\OrganizationRegisterController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendEmailVerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::as('auth.')->group(function (): void {
        Route::get('register', [RegisterController::class, 'create'])->name('register');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        Route::get('register/organization', [OrganizationRegisterController::class, 'create'])->name('register.organization');
        Route::post('register/organization', [OrganizationRegisterController::class, 'store'])->name('register.organization.store')->middleware([HandlePrecognitiveRequests::class]);

        Route::get('login', [LoginController::class, 'create'])->name('login');
        Route::post('login', [LoginController::class, 'store'])->middleware('throttle:login')->name('login.store');

        Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email')->middleware('throttle:password-reset-request');
        Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password/update', [ResetPasswordController::class, 'store'])->name('password.store')->middleware('throttle:password-reset');
    });
});

Route::middleware('auth')->group(function (): void {
    Route::as('auth.')->group(function (): void {
        Route::post('logout', LogoutController::class)->name('logout');
        Route::get('email/verify', [EmailVerificationController::class, 'index'])->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:10,1'])->name('verification.verify');
        Route::post('email/verification-notification', ResendEmailVerificationController::class)->middleware('throttle:5,1')->name('verification.send');
    });
});
