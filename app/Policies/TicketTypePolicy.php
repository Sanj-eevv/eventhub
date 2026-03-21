<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PreservedRoleList;
use App\Enums\TicketTypePermissions;
use App\Models\User;

final class TicketTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole([
            PreservedRoleList::SuperAdmin->value,
            PreservedRoleList::Admin->value,
            PreservedRoleList::OrganizationAdmin->value,
        ]);
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(TicketTypePermissions::AllowCreate);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(TicketTypePermissions::AllowUpdate);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(TicketTypePermissions::AllowDelete);
    }
}
