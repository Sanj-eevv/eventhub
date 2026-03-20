<?php

declare(strict_types=1);

namespace App\Enums;

enum UserPermissions: string
{
    case AllowCreate = 'user:create';
    case AllowDelete = 'user:delete';
    case AllowUpdate = 'user:update';
}
