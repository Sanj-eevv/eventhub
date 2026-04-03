<?php

declare(strict_types=1);

namespace App\Enums;

enum CheckInPermissions: string
{
    case Manage = 'check-in:manage';
}
