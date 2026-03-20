<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Event;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class SharedPermissionResource
{
    public function __construct(private readonly User $user) {}

    public static function make(User $user): self
    {
        return new self($user);
    }

    /** @return array{organization: array{viewAny: bool, create: bool}, user: array{viewAny: bool, create: bool}, role: array{viewAny: bool, create: bool}, event: array{viewAny: bool, create: bool}} */
    public function toArray(): array
    {
        return [
            'organization' => $this->abilities(Organization::class),
            'user' => $this->abilities(User::class),
            'role' => [
                'viewAny' => Gate::forUser($this->user)->allows('viewAny', Role::class),
                'create' => Gate::forUser($this->user)->allows('create', Role::class),
            ],
            'event' => $this->abilities(Event::class),
        ];
    }

    /** @return array{viewAny: bool, create: bool} */
    private function abilities(string $model): array
    {
        return [
            'viewAny' => Gate::forUser($this->user)->allows('viewAny', $model),
            'create' => Gate::forUser($this->user)->allows('create', $model),
        ];
    }
}
