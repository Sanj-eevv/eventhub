<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class OrderStatusChanged implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly Order $order) {}

    public function broadcastOn(): array
    {
        return [new PrivateChannel('order.'.$this->order->uuid)];
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'status' => $this->order->status->value,
            'paid_at' => $this->order->paid_at?->toISOString(),
            'tickets' => $this->order->relationLoaded('tickets')
                ? $this->order->tickets->map(fn ($ticket): array => [
                    'uuid' => $ticket->uuid,
                    'booking_reference' => $ticket->booking_reference,
                ])->all()
                : [],
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.status-changed';
    }
}
