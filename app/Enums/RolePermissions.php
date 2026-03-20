<?php

declare(strict_types=1);

namespace App\Enums;

enum RolePermissions: string
{
    case AllowCreate = 'role:create';
    case AllowDelete = 'role:delete';
    case AllowUpdate = 'role:update';
}
