<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;

final class PermissionService
{
    /** @return array<string, list<array{id: int, name: string, description: string}>> */
    public function getGroupedPermissions(?Role $role = null): array
    {
        $permissions = $role
            ? $role->permissions
            : Permission::query()->select('id', 'name', 'description')->get();

        return $permissions->mapToGroups(function (Permission $permission): array {
            [$model, $action] = explode(':', $permission->name, 2);

            return [$model => ['id' => $permission->id, 'name' => $action, 'description' => $permission->description]];
        })->toArray();
    }
}
