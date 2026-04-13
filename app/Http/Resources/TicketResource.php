<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Ticket */
final class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'booking_reference' => $this->booking_reference,
            'status' => $this->status->value,
            'attendee_name' => $this->attendee_name,
            'attendee_email' => $this->attendee_email,
            'qr_code_url' => $this->qr_code_path
                ? route('tickets.qr-code', ['ticket' => $this->uuid])
                : null,
            'checked_in_at' => $this->checked_in_at?->toISOString(),
            'event' => EventResource::make($this->whenLoaded('event')),
            'ticket_type' => TicketTypeResource::make($this->whenLoaded('ticketType')),
        ];
    }
}
