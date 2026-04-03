<?php

declare(strict_types=1);

namespace App\Enums;

enum SettingPermissions: string
{
    case Manage = 'setting:manage';
}
