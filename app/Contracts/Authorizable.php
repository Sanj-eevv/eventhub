<?php

declare(strict_types=1);

namespace App\Contracts;

use BackedEnum;

interface Authorizable
{
    public function hasPermission(BackedEnum $permission): bool;

    public function hasAllPermissions(BackedEnum ...$permissions): bool;

    public function hasAnyPermission(BackedEnum ...$permissions): bool;
}
