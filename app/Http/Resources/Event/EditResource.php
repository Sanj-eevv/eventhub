<?php

declare(strict_types=1);

namespace App\Http\Resources\Event;

use App\Http\Resources\MediaResource;
use App\Http\Resources\TicketTypeResource;
use App\Models\Event;
use App\Support\DateFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Event */
final class EditResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'organization_uuid' => $this->organization->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'starts_at' => $this->starts_at->format(DateFormat::DATETIME_LOCAL),
            'ends_at' => $this->ends_at?->format(DateFormat::DATETIME_LOCAL),
            'timezone' => $this->timezone,
            'location' => $this->location,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'organization' => $this->whenLoaded('organization', fn () => [
                'uuid' => $this->organization->uuid,
                'title' => $this->organization->title,
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'uuid' => $this->user->uuid,
                'name' => $this->user->name,
            ]),
            'ticket_types' => $this->whenLoaded('ticketTypes', fn () => TicketTypeResource::collection($this->ticketTypes)->resolve()),
            'media' => $this->whenLoaded('media', fn () => MediaResource::collection($this->media)->resolve()),
            'cover_image' => $this->whenLoaded(
                'media',
                fn () => $this->media->firstWhere('is_cover', true)
                ? new MediaResource($this->media->firstWhere('is_cover', true))
                : null,
            ),
        ];
    }
}
