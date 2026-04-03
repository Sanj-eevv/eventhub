<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\UserPermissions;
use App\Models\User;

final class UserPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(
            UserPermissions::Create,
            UserPermissions::Update,
            UserPermissions::Delete,
        );
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(UserPermissions::Create);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(UserPermissions::Update);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(UserPermissions::Delete);
    }
}
