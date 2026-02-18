<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PreservedRoleList;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->upsert([
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Super Admin',
                'slug' => PreservedRoleList::SUPER_ADMIN->value,
                'description' => 'Superadmin of a system',
                'preserved' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Admin',
                'slug' => PreservedRoleList::ADMIN->value,
                'description' => 'Admin of a system',
                'preserved' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Organization Admin',
                'slug' => PreservedRoleList::ORGANIZATION_ADMIN->value,
                'description' => 'Admin of an organization',
                'preserved' => true,
            ],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'user',
                'slug' => PreservedRoleList::USER->value,
                'description' => 'Normal user',
                'preserved' => true,
            ],
        ], ['slug'], ['name', 'description', 'updated_at']);
    }
}
