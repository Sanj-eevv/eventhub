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
            RolePermissions::AllowCreate,
            RolePermissions::AllowUpdate,
            RolePermissions::AllowDelete,
        ]);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(RolePermissions::AllowCreate);
    }

    public function update(User $user, Role $role): bool
    {
        return $user->hasPermission(RolePermissions::AllowUpdate) && $role->slug !== PreservedRoleList::SuperAdmin->value;
    }

    public function delete(User $user, Role $role): bool
    {
        return $user->hasPermission(RolePermissions::AllowDelete) && ! $role->preserved;
    }
}
