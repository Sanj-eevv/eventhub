<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

final class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->upsert([
            [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Superadmin of a system',
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Admin of a system',
            ],
            [
                'name' => 'user',
                'slug' => 'user',
                'description' => 'Normal user',
            ],
        ], ['slug'], ['name', 'description', 'updated_at']);
    }
}
