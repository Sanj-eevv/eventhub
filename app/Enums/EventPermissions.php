<?php

declare(strict_types=1);

namespace App\Enums;

enum EventPermissions: string
{
    case View = 'event:view';
    case Create = 'event:create';
    case Update = 'event:update';
    case Delete = 'event:delete';
    case Publish = 'event:publish';
    case Cancel = 'event:cancel';
}
