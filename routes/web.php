<?php

declare(strict_types=1);

use App\Enums\PreservedRoleList;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\OrganizationController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('home');

Route::middleware('guest')->group(function (): void {
    Route::as('auth.')->group(function (): void {
        Route::get('register', [RegisterController::class, 'register'])->name('register');
        Route::post('register', [RegisterController::class, 'store'])->name('register.store');
        Route::get('register/organization', [RegisterController::class, 'registerOrganization'])->name('register.organization');
        Route::post('register/organization', [RegisterController::class, 'storeOrganization'])->name('register.organization.store')->middleware([HandlePrecognitiveRequests::class]);

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
    $preservedAdminRolesStr = implode(',', [PreservedRoleList::SUPER_ADMIN->value, PreservedRoleList::ADMIN->value, PreservedRoleList::ORGANIZATION_ADMIN->value]);
    Route::middleware(["role:{$preservedAdminRolesStr}", 'verified:auth.verification.notice'])->prefix('dashboard')->as('dashboard.')->group(function (): void {
        Route::get('/', DashboardController::class)->name('index');
        Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::resource('roles', RoleController::class);
        Route::resource('organizations', OrganizationController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::post('organizations/{organization}/{action}', [OrganizationController::class, 'confirmStatus'])->name('organizations.confirm-status');
        Route::resource('events', EventController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('events/{event}/publish', [EventController::class, 'publish'])->name('events.publish');
        Route::post('events/{event}/unpublish', [EventController::class, 'unpublish'])->name('events.unpublish');
        Route::post('events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
    });
});
