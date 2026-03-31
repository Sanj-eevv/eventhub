<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Enums\TicketStatus;
use App\Events\OrderCompleted;
use App\Jobs\GenerateTicketQrCodesJob;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Bus\Dispatcher as BusDispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;

final class CompleteOrderAction
{
    public function __construct(
        private readonly DatabaseManager $databaseManager,
        private readonly Dispatcher $dispatcher,
        private readonly BusDispatcher $busDispatcher,
    ) {}

    public function execute(Order $order): Order
    {
        if (OrderStatus::Paid === $order->status) {
            return $order;
        }

        $this->databaseManager->transaction(function () use ($order): void {
            $order->update([
                'status' => OrderStatus::Paid,
                'paid_at' => CarbonImmutable::now(),
            ]);

            $order->tickets()
                ->where('status', TicketStatus::Pending)
                ->update(['status' => TicketStatus::Active]);
        });

        $this->dispatcher->dispatch(new OrderCompleted($order));
        $this->busDispatcher->dispatch(new GenerateTicketQrCodesJob($order));

        return $order;
    }
}
