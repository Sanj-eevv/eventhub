<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'qr_code_path' => $this->qr_code_path,
            'checked_in_at' => $this->checked_in_at,
            'event' => [
                'title' => $this->event->title,
            ],
            'ticket_type' => [
                'name' => $this->ticketType->name,
                'description' => $this->ticketType->description,
            ],
        ];
    }
}
