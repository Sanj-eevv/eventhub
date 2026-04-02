<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

final class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissionData = [
            ['name' => 'event:create', 'description' => 'Create event'],
            ['name' => 'event:update', 'description' => 'Update event'],
            ['name' => 'event:delete', 'description' => 'Delete event'],
            ['name' => 'organization:create', 'description' => 'Create organization'],
            ['name' => 'organization:update', 'description' => 'Update organization'],
            ['name' => 'organization:delete', 'description' => 'Delete organization'],
            ['name' => 'user:create', 'description' => 'Create user'],
            ['name' => 'user:update', 'description' => 'Update user'],
            ['name' => 'user:delete', 'description' => 'Delete user'],
            ['name' => 'role:create', 'description' => 'Create role'],
            ['name' => 'role:update', 'description' => 'Update role'],
            ['name' => 'role:delete', 'description' => 'Delete role'],
            ['name' => 'order:view', 'description' => 'View order'],
            ['name' => 'order:cancel', 'description' => 'Cancel order'],
            ['name' => 'setting:manage', 'description' => 'Manage platform settings'],
        ];

        Permission::upsert($permissionData, ['name'], ['description', 'updated_at']);

        $permissions = Permission::query()->pluck('id', 'name');

        $allPermissionIds = $permissions->values()->all();
        $adminPermissionIds = $permissions->except(['setting:manage'])->values()->all();
        $orgAdminPermissionIds = $permissions->only([
            'event:create', 'event:update', 'event:delete',
            'user:create', 'user:update', 'user:delete',
        ])->values()->all();

        Role::superAdminRole()->permissions()->sync($allPermissionIds);
        Role::adminRole()->permissions()->sync($adminPermissionIds);
        Role::organizationAdminRole()->permissions()->sync($orgAdminPermissionIds);
    }
}
