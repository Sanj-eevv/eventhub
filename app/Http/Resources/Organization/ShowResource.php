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
            'status' => $this->status->value,
            'verified_at' => $this->verified_at ? formatUserTime($this->verified_at, 'd M Y') : null,
            'created_at' => formatUserTime($this->created_at, 'd M Y'),
        ];
    }
}
