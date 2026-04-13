<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\User;

trait ScopedToOrganization
{
    protected function withinOrganization(User $user, object $resource): bool
    {
        return $user->organization_id === $resource->organization_id;
    }
}
