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
                UserPermissions::ALLOW_CREATE,
                UserPermissions::ALLOW_UPDATE,
                UserPermissions::ALLOW_DELETE,
            ],
        );
    }

    public function view(User $user, User $target): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(UserPermissions::ALLOW_CREATE);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(UserPermissions::ALLOW_UPDATE);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(UserPermissions::ALLOW_DELETE);
    }
}
