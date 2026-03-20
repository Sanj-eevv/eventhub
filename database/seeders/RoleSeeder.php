<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PreservedRoleList;
use App\Models\Role;
use Illuminate\Database\Seeder;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->upsert([
            [
                'name' => 'Super Admin',
                'slug' => PreservedRoleList::SuperAdmin->value,
                'description' => 'Superadmin of a system',
                'preserved' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => PreservedRoleList::Admin->value,
                'description' => 'Admin of a system',
                'preserved' => true,
            ],
            [
                'name' => 'Organization Admin',
                'slug' => PreservedRoleList::OrganizationAdmin->value,
                'description' => 'Admin of an organization',
                'preserved' => true,
            ],
            [
                'name' => 'User',
                'slug' => PreservedRoleList::User->value,
                'description' => 'Normal user',
                'preserved' => true,
            ],
        ], ['slug'], ['name', 'description', 'updated_at']);
    }
}
