<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationPermissions;
use App\Models\Organization;
use App\Models\User;

final class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            OrganizationPermissions::AllowCreate,
            OrganizationPermissions::AllowUpdate,
            OrganizationPermissions::AllowDelete,
        ]);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::AllowCreate);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::AllowUpdate);
    }

    public function approve(User $user, Organization $organization): bool
    {
        return $user->hasPermission(OrganizationPermissions::AllowUpdate);
    }

    public function reject(User $user, Organization $organization): bool
    {
        return $user->hasPermission(OrganizationPermissions::AllowUpdate);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::AllowDelete);
    }
}
