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

    public function view(User $user, ?User $target = null): bool
    {
        if ( ! $this->viewAny($user)) {
            return false;
        }

        return ! $target instanceof User
            || null === $user->organization_id
            || $user->organization_id === $target->organization_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(UserPermissions::Create);
    }

    public function update(User $user, ?User $target = null): bool
    {
        if ( ! $user->hasPermission(UserPermissions::Update)) {
            return false;
        }

        return ! $target instanceof User
            || null === $user->organization_id
            || $user->organization_id === $target->organization_id;
    }

    public function delete(User $user, ?User $target = null): bool
    {
        if ( ! $user->hasPermission(UserPermissions::Delete)) {
            return false;
        }

        return ! $target instanceof User
            || null === $user->organization_id
            || $user->organization_id === $target->organization_id;
    }
}
