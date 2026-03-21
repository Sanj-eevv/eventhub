<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderPermissions: string
{
    case AllowView = 'order:view';
}
