<?php

declare(strict_types=1);

namespace App\Enums;

enum EventPermissions: string
{
    case AllowCreate = 'event:create';
    case AllowDelete = 'event:delete';
    case AllowUpdate = 'event:update';
}
