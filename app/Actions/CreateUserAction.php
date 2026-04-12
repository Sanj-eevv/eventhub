<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\UserData;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;

final readonly class CreateUserAction
{
    public function __construct(private Hasher $hash) {}

    public function execute(UserData $userData): User
    {
        $role = Role::query()->where('slug', $userData->role_slug)->firstOrFail();
        $organization = $userData->organization_uuid
            ? Organization::query()->where('uuid', $userData->organization_uuid)->firstOrFail()
            : null;

        return User::query()->create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => $this->hash->make($userData->password),
            'role_id' => $role->id,
            'organization_id' => $organization?->id,
        ]);
    }
}
