<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Order */
final class OrderResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label(), 'color' => $this->status->color()],
            'currency' => $this->currency,
            'total' => $this->total,
            'refund_status' => $this->refund_status?->value,
            'reserved_at' => $this->reserved_at?->toISOString(),
            'expires_at' => $this->expires_at?->toISOString(),
            'paid_at' => $this->paid_at?->toISOString(),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'refunded_at' => $this->refunded_at?->toISOString(),
            'event' => EventResource::make($this->whenLoaded('event')),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
        ];
    }
}
