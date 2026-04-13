<?php

declare(strict_types=1);

namespace App\Actions;

use App\DataTransferObjects\UserData;
use App\Models\Organization;
use App\Models\Role;
use App\Models\User;

final class UpdateUserAction
{
    public function __invoke(User $user, UserData $data): User
    {
        $role = Role::query()->where('slug', $data->role_slug)->firstOrFail();
        $organization = $data->organization_uuid
            ? Organization::query()->where('uuid', $data->organization_uuid)->firstOrFail()
            : null;

        $user->update([
            'name' => $data->name,
            'email' => $data->email,
            'role_id' => $role->id,
            'organization_id' => $organization?->id,
        ]);

        return $user;
    }
}
