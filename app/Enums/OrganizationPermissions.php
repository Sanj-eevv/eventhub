<?php

declare(strict_types=1);

namespace App\Enums;

enum OrganizationPermissions: string
{
    case Create = 'organization:create';
    case Update = 'organization:update';
    case Delete = 'organization:delete';
}
