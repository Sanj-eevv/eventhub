<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\User\ShowResource;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Event */
final class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'starts_at' => $this->starts_at->toISOString(),
            'ends_at' => $this->ends_at->toISOString(),
            'timezone' => $this->timezone,
            'venue_name' => $this->venue_name,
            'address' => $this->address,
            'zip' => $this->zip,
            'map_url' => $this->map_url,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),

            'organization' => $this->whenLoaded('organization', fn () => [
                'uuid' => $this->organization->uuid,
                'title' => $this->organization->title,
                'status' => ['value' => $this->organization->status->value, 'label' => $this->organization->status->label()],
            ]),
            'user' => ShowResource::make($this->whenLoaded('user')),
            'ticket_types' => TicketTypeResource::collection($this->whenLoaded('ticketTypes')),
            'ticket_types' => $this->whenLoaded('ticketTypes', fn () => TicketTypeResource::collection($this->ticketTypes)->resolve()),
            'media' => $this->whenLoaded('media', fn () => MediaResource::collection($this->media)->resolve()),
        ];
    }
}
