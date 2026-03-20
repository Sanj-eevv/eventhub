<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserPermissions;
use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(
            [
                UserPermissions::AllowCreate,
                UserPermissions::AllowUpdate,
                UserPermissions::AllowDelete,
            ],
        );
    }

    public function view(User $user, User $target): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(UserPermissions::AllowCreate);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(UserPermissions::AllowUpdate);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(UserPermissions::AllowDelete);
    }
}
