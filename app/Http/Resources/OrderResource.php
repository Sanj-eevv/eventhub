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
            'total_formatted' => '$'.number_format($this->total / 100, 2),
            'reserved_at' => $this->reserved_at,
            'expires_at' => $this->expires_at,
            'paid_at' => $this->paid_at,
            'event' => [
                'title' => $this->event->title,
                'slug' => $this->event->slug,
            ],
            'tickets' => $this->tickets->map(fn (mixed $ticket) => [
                'uuid' => $ticket->uuid,
                'booking_reference' => $ticket->booking_reference,
                'status' => $ticket->status->value,
                'qr_code_path' => $ticket->qr_code_path,
                'attendee_name' => $ticket->attendee_name,
                'ticket_type' => $ticket->ticketType->name,
            ]),
        ];
    }
}
