<?php

declare(strict_types=1);

namespace App\Enums;

enum EventPermissions: string
{
    case ALLOW_CREATE = 'event:create';
    case ALLOW_DELETE = 'event:delete';
    case ALLOW_UPDATE = 'event:update';
}
