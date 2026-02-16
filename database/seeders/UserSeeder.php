<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class UserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::query()->firstOrCreate([
            'slug' => 'super-admin',
        ], [
            'name' => 'Super Admin',
            'description' => 'Superadmin of a system',
        ]);
        User::query()->create([
            'role_id' => $role->id,
            'name' => 'Sanjeev',
            'email' => 'sanjeevvsanjeev1@gmail.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
