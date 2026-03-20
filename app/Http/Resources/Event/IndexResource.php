<?php

declare(strict_types=1);

namespace App\Http\Resources\Event;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class IndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'organization' => $this->organization_title ? ['uuid' => $this->organization_uuid, 'title' => $this->organization_title] : null,
            'starts_at' => formatUserTime($this->starts_at, 'd M Y H:i'),
            'ends_at' => $this->ends_at ? formatUserTime($this->ends_at, 'd M Y H:i') : null,
            'created_at' => formatUserTime($this->created_at, 'd M Y'),
        ];
    }
}
