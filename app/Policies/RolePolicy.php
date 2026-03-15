<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PreservedRoleList;
use App\Enums\RolePermissions;
use App\Models\Role;
use App\Models\User;

final class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            RolePermissions::ALLOW_CREATE,
            RolePermissions::ALLOW_UPDATE,
            RolePermissions::ALLOW_DELETE,
        ]);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(RolePermissions::ALLOW_CREATE);
    }

    public function update(User $user, Role $role): bool
    {
        dd($role);

        return $user->hasPermission(RolePermissions::ALLOW_UPDATE) && $role->slug !== PreservedRoleList::SUPER_ADMIN->value;
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermission(RolePermissions::ALLOW_DELETE) && ! $role->preserved;
    }
}
