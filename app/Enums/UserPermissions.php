<?php

declare(strict_types=1);

namespace App\Enums;

enum UserPermissions: string
{
    case ALLOW_CREATE = 'user:create';
    case ALLOW_DELETE = 'user:delete';
    case ALLOW_UPDATE = 'user:update';
}
