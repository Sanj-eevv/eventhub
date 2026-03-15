<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Gate;

final class SharedPermissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'organization' => $this->abilities(Organization::class),
            'user' => $this->abilities(User::class),
            'role' => [
                'viewAny' => Gate::allows('viewAny', Role::class),
                'create' => Gate::allows('create', Role::class),
            ],
            'event' => $this->abilities(Event::class),
        ];
    }

    protected function abilities(string $model): array
    {
        return [
            'viewAny' => Gate::allows('viewAny', $model),
            'create' => Gate::allows('create', $model),
        ];
    }
}
