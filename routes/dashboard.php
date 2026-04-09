<?php

declare(strict_types=1);

use App\Http\Controllers\Dashboard\ApproveOrganizationController;
use App\Http\Controllers\Dashboard\CancelEventController;
use App\Http\Controllers\Dashboard\CancelOrderController;
use App\Http\Controllers\Dashboard\CheckInController;
use App\Http\Controllers\Dashboard\EventController;
use App\Http\Controllers\Dashboard\EventMediaController;
use App\Http\Controllers\Dashboard\NotificationsController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\OrganizationController;
use App\Http\Controllers\Dashboard\PublishEventController;
use App\Http\Controllers\Dashboard\RejectOrganizationController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\ScanTicketController;
use App\Http\Controllers\Dashboard\SetEventMediaCoverController;
use App\Http\Controllers\Dashboard\SettingController;
use App\Http\Controllers\Dashboard\UnpublishEventController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'can:access-dashboard', 'verified:auth.verification.notice'])
    ->prefix('dashboard')
    ->as('dashboard.')
    ->group(function (): void {
        Route::get('/', DashboardController::class)->name('index');
        Route::get('notifications', NotificationsController::class)->name('notifications.index');

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
        Route::get('events/{event}/check-in', [CheckInController::class, 'index'])->name('events.check-in');
        Route::post('events/{event}/check-in', ScanTicketController::class)->name('events.check-in.scan');

        Route::resource('orders', OrderController::class)->only(['index', 'show']);
        Route::delete('orders/{order:uuid}', CancelOrderController::class)->name('orders.cancel');

        Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });
