<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;

final class PermissionService
{
    public function getGrouppedPermissions(Role|string|null $role = null): array
    {
        if ($role) {
            $role->loadMissing('permissions:id,name,description');
            $permissions = $role instanceof Role ? $role->permissions : (Role::query()->with('permissions:id,name,description')->where('slug', $role)->first()?->permissions ?? collect([]));
        }
        $permissions ??= Permission::query()->select('id', 'name', 'description')->get();
        $groupedPermissions = [];
        $permissions->each(function (Permission $permission) use (&$groupedPermissions): void {
            [$model, $action] = explode(':', $permission->name, 2);
            $groupedPermissions[$model][] = [
                'id' => $permission->id,
                'name' => $action,
                'description' => $permission->description,
            ];
        });

        return $groupedPermissions;
    }
}
