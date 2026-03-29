<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;

final class ExpireOrderAction
{
    public function __construct(private readonly DatabaseManager $db) {}

    public function execute(Order $order): void
    {
        if (OrderStatus::Reserved !== $order->status) {
            return;
        }

        $this->db->transaction(function () use ($order): void {
            $order->update([
                'status' => OrderStatus::Expired,
                'cancelled_at' => CarbonImmutable::now(),
            ]);

            $order->tickets()
                ->where('status', TicketStatus::Pending)
                ->update(['status' => TicketStatus::Cancelled]);
        });
    }
}
