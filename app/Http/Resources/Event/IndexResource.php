<?php

declare(strict_types=1);

namespace App\Http\Resources\Event;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Event
 *
 * @property-read string $organization_uuid
 * @property-read string $organization_title
 */
final class IndexResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label(), 'color' => $this->status->color()],
            'organization' => $this->organization_title ? ['uuid' => $this->organization_uuid, 'title' => $this->organization_title] : null,
            'venue_name' => $this->venue_name,
            'address' => $this->address,
            'starts_at' => $this->starts_at->toISOString(),
            'ends_at' => $this->ends_at?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
