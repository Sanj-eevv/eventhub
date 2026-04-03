<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationPermissions;
use App\Models\User;

final class OrganizationPolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(
            OrganizationPermissions::Create,
            OrganizationPermissions::Update,
            OrganizationPermissions::Delete,
        );
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::Create);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::Update);
    }

    public function approve(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::Update);
    }

    public function reject(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::Update);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::Delete);
    }
}
