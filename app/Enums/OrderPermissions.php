<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderPermissions: string
{
    case View = 'order:view';
    case Cancel = 'order:cancel';
}
