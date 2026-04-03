<?php

declare(strict_types=1);

namespace App\Enums;

enum RolePermissions: string
{
    case Create = 'role:create';
    case Update = 'role:update';
    case Delete = 'role:delete';
}
