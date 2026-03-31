<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Database\DatabaseManager;

final class ExpireOrderAction
{
    public function __construct(private readonly DatabaseManager $databaseManager) {}

    public function execute(Order $order): void
    {
        if (OrderStatus::Reserved !== $order->status) {
            return;
        }

        $this->databaseManager->transaction(function () use ($order): void {
            $order->tickets()->delete();
            $order->delete();
        });
    }
}
