<?php

declare(strict_types=1);

namespace App\Http\Resources\User;

use App\Http\Resources\Organization\ShowResource as OrganizationShowResource;
use App\Http\Resources\Role\ShowResource as RoleShowResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified' => (bool) $this->email_verified_at,
            'created_at' => $this->created_at->toISOString(),
            'role' => RoleShowResource::make($this->whenLoaded('role')),
            'organization' => OrganizationShowResource::make($this->whenLoaded('organization')),
        ];
    }
}
