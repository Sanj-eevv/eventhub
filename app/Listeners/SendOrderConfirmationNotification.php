<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderCompleted;
use App\Notifications\OrderConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

final class SendOrderConfirmationNotification implements ShouldQueue
{
    public function handle(OrderCompleted $event): void
    {
        $order = $event->order->loadMissing('user');

        $order->user->notify(new OrderConfirmedNotification($order));
    }
}
