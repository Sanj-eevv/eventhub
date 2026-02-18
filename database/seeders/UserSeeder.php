<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::SuperAdminRole();
        User::query()->create([
            'role_id' => $superAdmin->id,
            'name' => 'Sanjeev',
            'email' => 'sanjeevvsanjeev1@gmail.com',
            'password' => 'password',
            'email_verified_at' => now(),
        ]);
    }
}
