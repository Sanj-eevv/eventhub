<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'currency' => $this->currency,
            'total' => $this->total,
            'reserved_at' => $this->reserved_at?->toISOString(),
            'expires_at' => $this->expires_at?->toISOString(),
            'paid_at' => $this->paid_at?->toISOString(),
            'event' => EventResource::make($this->whenLoaded('event')),
            'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
        ];
    }
}
