<?php

declare(strict_types=1);

namespace App\Http\Resources\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'description' => $this->description,
            'starts_at' => formatUserTime($this->starts_at, 'd M Y H:i'),
            'ends_at' => $this->ends_at ? formatUserTime($this->ends_at, 'd M Y H:i') : null,
            'timezone' => $this->timezone,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'created_at' => formatUserTime($this->created_at, 'd M Y'),
            'organization' => $this->whenLoaded('organization', fn () => [
                'uuid' => $this->organization->uuid,
                'title' => $this->organization->title,
                'status' => ['value' => $this->organization->status->value, 'label' => $this->organization->status->label()],
            ]),
            'user' => $this->whenLoaded('user', fn () => [
                'uuid' => $this->user->uuid,
                'name' => $this->user->name,
            ]),
        ];
    }
}
