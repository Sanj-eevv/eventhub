<?php

declare(strict_types=1);

namespace App\Events;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class OrderReserved implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Order $order,
    ) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('event.'.$this->order->event->uuid)];
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'order_uuid' => $this->order->uuid,
            'reserved_count' => $this->order->event->orders()->where('status', OrderStatus::Reserved)->count(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.reserved';
    }
}
