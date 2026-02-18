<?php

declare(strict_types=1);

namespace App\Enums;

enum PreservedRoleList: string
{
    case SUPER_ADMIN = 'super-admin';
    case ADMIN = 'admin';
    case ORGANIZATION_ADMIN = 'organization-admin';
    case USER = 'user';
}
