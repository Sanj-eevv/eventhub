<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\SettingPermissions;
use App\Models\User;

final class SettingPolicy extends BasePolicy
{
    public function update(User $user): bool
    {
        return $user->hasPermission(SettingPermissions::Manage);
    }
}
