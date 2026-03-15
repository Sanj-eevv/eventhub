<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\OrganizationPermissions;
use App\Enums\OrganizationStatus;
use App\Models\Organization;
use App\Models\User;

final class OrganizationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission([
            OrganizationPermissions::ALLOW_CREATE,
            OrganizationPermissions::ALLOW_UPDATE,
            OrganizationPermissions::ALLOW_DELETE,
        ]);
    }

    public function view(User $user): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::ALLOW_CREATE);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::ALLOW_UPDATE);
    }

    public function approve(User $user, Organization $organization): bool
    {
        return OrganizationStatus::Pending === $organization->status;
    }

    public function reject(User $user, Organization $organization): bool
    {
        return OrganizationStatus::Pending === $organization->status;
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(OrganizationPermissions::ALLOW_DELETE);
    }
}
