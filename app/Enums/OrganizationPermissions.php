<?php

declare(strict_types=1);

namespace App\Enums;

enum OrganizationPermissions: string
{
    case ALLOW_CREATE = 'organization:create';
    case ALLOW_DELETE = 'organization:delete';
    case ALLOW_UPDATE = 'organization:update';
}
