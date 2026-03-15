<?php

declare(strict_types=1);

namespace App\Services;

use App\DataTransferObjects\RoleDto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class RoleService
{
    public function create(RoleDto $data, array $permissionIds = []): Role
    {
        $role = Role::query()->create(omit('permissions', $data->toArray()));
        $role->permissions()->sync($permissionIds);

        return $role;
    }

    public function update(Role $role, RoleDto $data): Role
    {
        tap($role)->update(omit('permissions', $data->toArray()));
        $role->permissions()->sync($data->permissions);

        return $role;
    }

    public function delete(Role $role): void
    {
        DB::transaction(function () use ($role): void {
            $defaultRole = Role::userRole();
            User::withTrashed()->where('role_id', $role->id)->update([
                'role_id' => $defaultRole->id,
            ]);
            $role->delete();
        });
    }
}
