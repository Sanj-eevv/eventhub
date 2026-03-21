<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Event;
use App\Support\DateFormat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Event */
final class EventResource extends JsonResource
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
            'ticket_types' => $this->whenLoaded('ticketTypes', fn () => $this->ticketTypes->map(fn ($ticketType) => [
                'uuid' => $ticketType->uuid,
                'name' => $ticketType->name,
                'price' => $ticketType->price / 100,
                'capacity' => $ticketType->capacity,
                'max_per_user' => $ticketType->max_per_user,
                'sale_starts_at' => $ticketType->sale_starts_at?->format(DateFormat::DATETIME_LOCAL),
                'sale_ends_at' => $ticketType->sale_ends_at?->format(DateFormat::DATETIME_LOCAL),
            ])->all()),
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
