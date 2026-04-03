<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\RolePermissions;
use App\Models\Role;
use App\Models\User;

final class RolePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(
            RolePermissions::Create,
            RolePermissions::Update,
            RolePermissions::Delete,
        );
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(RolePermissions::Create);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermission(RolePermissions::Update) && ! $role->preserved;
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermission(RolePermissions::Delete) && ! $role->preserved;
    }
}
