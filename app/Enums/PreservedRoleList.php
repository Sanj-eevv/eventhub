<?php

declare(strict_types=1);

namespace App\Enums;

enum PreservedRoleList: string
{
    case SuperAdmin = 'super-admin';
    case Admin = 'admin';
    case OrganizationAdmin = 'organization-admin';
    case User = 'user';

    /** @return list<string> */
    public static function adminRoles(): array
    {
        return [
            self::SuperAdmin->value,
            self::Admin->value,
            self::OrganizationAdmin->value,
        ];
    }

    public static function adminRolesString(): string
    {
        return implode(',', self::adminRoles());
    }
}
