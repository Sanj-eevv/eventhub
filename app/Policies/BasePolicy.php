<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PreservedRoleList;
use App\Models\User;

abstract class BasePolicy
{
    final public function before(User $user, string $ability): ?bool
    {
        if (in_array($ability, $this->attendeeAbilities(), true)) {
            return null;
        }

        return $user->hasAnyRole(PreservedRoleList::SuperAdmin) ? true : null;
    }

    protected function attendeeAbilities(): array
    {
        return [];
    }
}
