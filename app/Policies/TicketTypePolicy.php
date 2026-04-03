<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\TicketTypePermissions;
use App\Models\User;

final class TicketTypePolicy extends BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(
            TicketTypePermissions::Create,
            TicketTypePermissions::Update,
            TicketTypePermissions::Delete,
        );
    }

    public function create(User $user): bool
    {
        return $user->hasPermission(TicketTypePermissions::Create);
    }

    public function update(User $user): bool
    {
        return $user->hasPermission(TicketTypePermissions::Update);
    }

    public function delete(User $user): bool
    {
        return $user->hasPermission(TicketTypePermissions::Delete);
    }
}
