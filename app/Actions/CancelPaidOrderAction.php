<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ActivityEvent;
use App\Enums\OrderStatus;
use App\Enums\RefundStatus;
use App\Enums\TicketStatus;
use App\Events\OrderCancelled;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Order;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Filesystem\FilesystemManager;

final readonly class CancelPaidOrderAction
{
    public function __construct(
        private DatabaseManager $databaseManager,
        private Dispatcher $dispatcher,
        private FilesystemManager $filesystemManager,
        private RecordActivityAction $recordActivityAction,
    ) {}

    public function execute(Order $order, ?User $causer = null): void
    {
        if (OrderStatus::Paid !== $order->status) {
            throw new InvalidStatusTransitionException($order->status, OrderStatus::Cancelled);
        }

        $this->databaseManager->transaction(function () use ($order): void {
            $order->update([
                'status' => OrderStatus::Cancelled,
                'cancelled_at' => CarbonImmutable::now(),
                'refund_status' => RefundStatus::Pending,
            ]);

            $order->tickets()
                ->where('status', TicketStatus::Active)
                ->update(['status' => TicketStatus::Cancelled, 'qr_code_path' => null]);
        });

        $this->filesystemManager->disk('local')->deleteDirectory('tickets/'.$order->uuid);

        $this->recordActivityAction->execute(ActivityEvent::OrderCancelled, $order, $causer);

        $order->loadMissing('event');
        $this->dispatcher->dispatch(new OrderCancelled($order));
    }
}
