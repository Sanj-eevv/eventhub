<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class IndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'role' => ['slug' => $this->role_slug, 'name' => $this->role_name],
            'organization' => $this->organization_uuid ? ['uuid' => $this->organization_uuid, 'title' => $this->organization_title] : null,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
