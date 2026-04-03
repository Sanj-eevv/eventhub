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
use App\Http\Controllers\BrowseEventController;
use App\Http\Controllers\CancelPaidOrderController;
use App\Http\Controllers\CancelReservedOrderController;
use App\Http\Controllers\CheckoutConfirmationController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Dashboard\ApproveOrganizationController;
use App\Http\Controllers\Dashboard\CancelEventController;
use App\Http\Controllers\Dashboard\CancelOrderController as DashboardCancelOrderController;
use App\Http\Controllers\Dashboard\CheckInController;
use App\Http\Controllers\Dashboard\OrderController as DashboardOrderController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\EventMediaController;
use App\Http\Controllers\Dashboard\OrganizationController;
use App\Http\Controllers\Dashboard\PublishEventController;
use App\Http\Controllers\Dashboard\RejectOrganizationController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\ScanTicketController;
use App\Http\Controllers\Dashboard\SetEventMediaCoverController;
use App\Http\Controllers\Dashboard\UnpublishEventController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DownloadOrderTicketsPdfController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\ProcessPaymentController;
use App\Http\Controllers\ReserveTicketsController;
use App\Http\Controllers\TicketQrCodeController;
use App\Http\Controllers\Webhooks\StripeWebhookController;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('home');
Route::get('events', [BrowseEventController::class, 'index'])->name('events.index');
Route::get('events/{event:slug}', [BrowseEventController::class, 'show'])->name('events.show');

Route::post('webhooks/stripe', StripeWebhookController::class)->name('webhooks.stripe');

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

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::post('events/{event:slug}/reserve', ReserveTicketsController::class)->name('tickets.reserve');
    Route::get('checkout/{order:uuid}', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::delete('checkout/{order:uuid}', CancelReservedOrderController::class)->name('checkout.cancel');
    Route::post('checkout/{order:uuid}/pay', ProcessPaymentController::class)->name('checkout.pay');
    Route::get('checkout/{order:uuid}/confirmation', CheckoutConfirmationController::class)->name('checkout.confirmation');
    Route::get('my/orders', [MyOrderController::class, 'index'])->name('orders.index');
    Route::get('my/orders/{order:uuid}', [MyOrderController::class, 'show'])->name('orders.show');
    Route::delete('my/orders/{order:uuid}', CancelPaidOrderController::class)->name('orders.cancel');
    Route::get('my/orders/{order:uuid}/pdf', DownloadOrderTicketsPdfController::class)->name('orders.pdf');
    Route::get('my/tickets/{ticket:uuid}/qr-code', TicketQrCodeController::class)->name('tickets.qr-code');
});

Route::middleware('auth')->group(function (): void {
    Route::as('auth.')->group(function (): void {
        Route::post('logout', LogoutController::class)->name('logout');
        Route::get('email/verify', [EmailVerificationController::class, 'index'])->name('verification.notice');
        Route::get('email/verify/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:10,1'])->name('verification.verify');
        Route::post('email/verification-notification', ResendEmailVerificationController::class)->middleware('throttle:5,1')->name('verification.send');
    });
    Route::middleware(['can:access-dashboard', 'verified:auth.verification.notice'])->prefix('dashboard')->as('dashboard.')->group(function (): void {
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
        Route::post('events/{event}/media', [EventMediaController::class, 'store'])->name('events.media.store');
        Route::delete('events/{event}/media/{media:uuid}', [EventMediaController::class, 'destroy'])->name('events.media.destroy');
        Route::post('events/{event}/media/{media:uuid}/cover', SetEventMediaCoverController::class)->name('events.media.cover');

        Route::resource('orders', DashboardOrderController::class)->only(['index', 'show']);
        Route::delete('orders/{order:uuid}', DashboardCancelOrderController::class)->name('orders.cancel');
        Route::get('events/{event}/check-in', [CheckInController::class, 'index'])->name('events.check-in');
        Route::post('events/{event}/check-in', ScanTicketController::class)->name('events.check-in.scan');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
});
