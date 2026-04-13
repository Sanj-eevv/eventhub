<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Role\ShowResource as RoleShowResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin User */
final class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified' => (bool) $this->email_verified_at,
            'created_at' => $this->created_at?->toISOString(),
            'role' => RoleShowResource::make($this->whenLoaded('role')),
            'organization' => OrganizationResource::make($this->whenLoaded('organization')),
        ];
    }
}
