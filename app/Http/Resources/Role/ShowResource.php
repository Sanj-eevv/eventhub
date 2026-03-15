<?php

declare(strict_types=1);

namespace App\Http\Resources\Role;

use App\Http\Resources\PermissionResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class ShowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'description' => $this->description,
            'preserved' => (bool) $this->preserved,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}
