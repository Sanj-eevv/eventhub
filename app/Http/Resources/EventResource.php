<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'organization_id' => $this->organization_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'starts_at' => $this->starts_at->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),
            'timezone' => $this->timezone,
            'location' => $this->location,
            'tickets' => $this->tickets,
            'status' => $this->status->value,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'organization' => $this->whenLoaded('organization', fn () => [
                'id' => $this->organization->id,
                'uuid' => $this->organization->uuid,
                'title' => $this->organization->title,
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'id' => $this->user->id,
                'uuid' => $this->user->uuid,
                'name' => $this->user->name,
            ]),
        ];
    }
}
