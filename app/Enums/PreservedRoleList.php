<?php

declare(strict_types=1);

namespace App\Enums;

enum PreservedRoleList: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case OrganizationAdmin = 'organization-admin';
    case User = 'user';

    public static function adminRolesString(): string
    {
        return implode(',', [
            self::SuperAdmin->value,
            self::Admin->value,
            self::OrganizationAdmin->value,
        ]);
    }
}
