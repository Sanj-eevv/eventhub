<?php

declare(strict_types=1);

namespace App\Http\Resources\Role;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

final class IndexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'name' => $this->name,
            'preserved' => (bool) $this->preserved,
            'created_at' => $this->created_at->toISOString(),
            'can' => [
                'update' => Gate::allows('update', $this),
                'delete' => Gate::allows('delete', $this),
            ],
        ];
    }
}
