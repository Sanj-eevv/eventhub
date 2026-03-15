<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PreservedRoleList;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            ['name' => 'role:create', 'description' => 'Create user'],
            ['name' => 'role:update', 'description' => 'Update user'],
            ['name' => 'role:delete', 'description' => 'Delete user'],
        ];
        Permission::upsert($permissionData, ['name'], ['description', 'updated_at']);

        $roles = DB::table('roles')
            ->whereIn('slug', array_column(PreservedRoleList::cases(), 'value'))
            ->pluck('id', 'slug');

        $permissions = DB::table('permissions')->pluck('id', 'name');

        $adminPermissionNames = array_column($permissionData, 'name');
        $matrix = [
            PreservedRoleList::SUPER_ADMIN->value => $adminPermissionNames,
            PreservedRoleList::ADMIN->value => $adminPermissionNames,
            PreservedRoleList::ORGANIZATION_ADMIN->value => [
                'event:create',
                'event:update',
                'event:delete',
                'user:create',
                'user:update',
                'user:delete',
            ],
        ];
        foreach ($matrix as $roleSlug => $permissionNames) {
            $roleId = $roles[$roleSlug];
            foreach ($permissionNames as $permissionName) {
                $permissionId = $permissions[$permissionName];
                $rows[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                ];
            }
        }
        DB::table('roles_permissions')->insertOrIgnore($rows);

    }
}
