<?php

declare(strict_types=1);

namespace App\Http\Resources\Organization;

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
            'contact_address' => $this->contact_address,
            'contact_email' => $this->contact_email,
            'status' => ['value' => $this->status->value, 'label' => $this->status->label()],
            'created_at' => formatUserTime($this->created_at, 'd M Y'),
        ];
    }
}
