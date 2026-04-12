<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;

final class PermissionService
{
    /** @return array<string, list<array{id: int, name: string, description: string}>> */
    public function grouped(?Role $role = null): array
    {
        $permissions = $role instanceof Role
            ? $role->permissions
            : Permission::query()->select('id', 'name', 'description')->get();

        return $permissions->mapToGroups(function (Permission $permission): array {
            [$entity, $action] = explode(':', $permission->name, 2);

            return [$entity => ['id' => $permission->id, 'name' => $action, 'description' => $permission->description]];
        })->toArray();
    }
}
