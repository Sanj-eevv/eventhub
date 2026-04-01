<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\SettingPermissions;
use App\Models\User;

final class SettingPolicy
{
    public function update(User $user): bool
    {
        return $user->hasPermission(SettingPermissions::AllowManage);
    }
}
