<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Order;
use Illuminate\Database\DatabaseManager;

final readonly class CancelReservedOrderAction
{
    public function __construct(private DatabaseManager $databaseManager) {}

    public function __invoke(Order $order): void
    {
        if (OrderStatus::Reserved !== $order->status) {
            throw new InvalidStatusTransitionException($order->status, OrderStatus::Cancelled);
        }

        $this->databaseManager->transaction(function () use ($order): void {
            $order->tickets()->delete();
            $order->delete();
        });
    }
}
