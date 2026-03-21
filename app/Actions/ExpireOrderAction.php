<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class ExpireOrderAction
{
    public function execute(Order $order): void
    {
        if (OrderStatus::Reserved !== $order->status) {
            return;
        }

        DB::transaction(function () use ($order): void {
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
