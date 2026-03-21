<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Events\OrderCompleted;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\DB;

final class CompleteOrderAction
{
    public function __construct(private readonly Dispatcher $dispatcher) {}

    public function execute(Order $order): Order
    {
        if (OrderStatus::Paid === $order->status) {
            return $order;
        }

        DB::transaction(function () use ($order): void {
            $order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => CarbonImmutable::now(),
            ]);

            $order->tickets()
                ->where('status', TicketStatus::Pending)
                ->update(['status' => TicketStatus::Active]);
        });

        $this->dispatcher->dispatch(new OrderCompleted($order));

        return $order;
    }
}
