<?php

declare(strict_types=1);

namespace App\Enums;

enum OrganizationPermissions: string
{
    case AllowCreate = 'organization:create';
    case AllowDelete = 'organization:delete';
    case AllowUpdate = 'organization:update';
}
