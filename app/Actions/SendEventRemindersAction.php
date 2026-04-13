<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Event;
use App\Models\Order;
use App\Notifications\EventReminderNotification;
use Carbon\CarbonImmutable;

final class SendEventRemindersAction
{
    public function __invoke(): void
    {
        $windowStart = CarbonImmutable::now()->addHours(23);
        $windowEnd = CarbonImmutable::now()->addHours(25);

        Event::query()
            ->published()
            ->whereBetween('starts_at', [$windowStart, $windowEnd])
            ->with(['orders' => fn ($query) => $query->paid()->with('user')])
            ->each(function (Event $event): void {
                $event->orders->each(function (Order $order) use ($event): void {
                    $order->user->notify(new EventReminderNotification($event));
                });
            });
    }
}
