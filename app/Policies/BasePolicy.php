<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PreservedRoleList;
use App\Models\User;

abstract class BasePolicy
{
    final public function before(User $user): ?bool
    {
        return $user->hasAnyRole(PreservedRoleList::SuperAdmin) ? true : null;
    }
}
