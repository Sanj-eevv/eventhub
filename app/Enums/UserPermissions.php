<?php

declare(strict_types=1);

namespace App\Enums;

enum UserPermissions: string
{
    case Create = 'user:create';
    case Update = 'user:update';
    case Delete = 'user:delete';
}
