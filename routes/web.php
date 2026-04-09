<?php

declare(strict_types=1);

use App\Http\Controllers\BrowseEventController;
use App\Http\Controllers\CancelPaidOrderController;
use App\Http\Controllers\CancelReservedOrderController;
use App\Http\Controllers\CheckoutConfirmationController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\DownloadOrderTicketsPdfController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MarkAllNotificationsReadController;
use App\Http\Controllers\MarkNotificationReadController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProcessPaymentController;
use App\Http\Controllers\ReserveTicketsController;
use App\Http\Controllers\TicketQrCodeController;
use Illuminate\Support\Facades\Route;

Route::get('/', IndexController::class)->name('home');
Route::get('events', [BrowseEventController::class, 'index'])->name('events.index');
Route::get('events/{event:slug}', [BrowseEventController::class, 'show'])->name('events.show');

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
    Route::get('notifications', NotificationsController::class)->name('notifications.index');
    Route::patch('notifications/{notificationId}/read', MarkNotificationReadController::class)->name('notifications.read');
    Route::delete('notifications', MarkAllNotificationsReadController::class)->name('notifications.read-all');
});

require __DIR__.'/auth.php';
require __DIR__.'/dashboard.php';
