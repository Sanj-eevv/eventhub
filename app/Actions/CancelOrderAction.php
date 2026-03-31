<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;

final class CancelOrderAction
{
    public function __construct(private readonly DatabaseManager $databaseManager) {}

    public function execute(Order $order): void
    {
        if ( ! $order->status->canTransitionTo(OrderStatus::Cancelled)) {
            throw new InvalidStatusTransitionException($order->status, OrderStatus::Cancelled);
        }

        $this->databaseManager->transaction(function () use ($order): void {
            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => CarbonImmutable::now(),
            ]);

            $order->tickets()
                ->whereIn('status', [TicketStatus::Pending, TicketStatus::Active])
                ->update(['status' => TicketStatus::Cancelled]);
        });
    }
}
