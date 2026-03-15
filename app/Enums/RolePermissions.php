<?php

declare(strict_types=1);

namespace App\Enums;

enum RolePermissions: string
{
    case ALLOW_CREATE = 'role:create';
    case ALLOW_DELETE = 'role:delete';
    case ALLOW_UPDATE = 'role:update';
}
