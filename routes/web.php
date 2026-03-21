<?php

declare(strict_types=1);

use App\Enums\PreservedRoleList;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\OrganizationRegisterController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResendEmailVerificationController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Dashboard\ApproveOrganizationController;
use App\Http\Controllers\Dashboard\CancelEventController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\OrganizationController;
use App\Http\Controllers\Dashboard\PublishEventController;
use App\Http\Controllers\Dashboard\RejectOrganizationController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UnpublishEventController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IndexController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('home');

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
    Route::middleware(['role:'.PreservedRoleList::adminRolesString(), 'verified:auth.verification.notice'])->prefix('dashboard')->as('dashboard.')->group(function (): void {
        Route::get('/', DashboardController::class)->name('index');
        Route::resource('users', UserController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::resource('roles', RoleController::class);
        Route::resource('organizations', OrganizationController::class)->only(['index', 'show', 'store', 'update', 'destroy']);
        Route::post('organizations/{organization}/approve', ApproveOrganizationController::class)->name('organizations.approve');
        Route::post('organizations/{organization}/reject', RejectOrganizationController::class)->name('organizations.reject');
        Route::resource('events', EventController::class)->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('events/{event}/publish', PublishEventController::class)->name('events.publish');
        Route::post('events/{event}/unpublish', UnpublishEventController::class)->name('events.unpublish');
        Route::post('events/{event}/cancel', CancelEventController::class)->name('events.cancel');
    });
});
