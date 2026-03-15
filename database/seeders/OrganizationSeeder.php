<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

final class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $orgAdmin = Role::organizationAdminRole();
        Organization::factory()
            ->count(10)
            ->approved()
            ->create()
            ->each(function (Organization $organization) use ($orgAdmin): void {
                User::factory()->create([
                    'organization_id' => $organization->id,
                    'role_id' => $orgAdmin->id,
                ]);
            });

        Organization::factory()
            ->count(10)
            ->create()
            ->each(function (Organization $organization) use ($orgAdmin): void {
                User::factory()->create([
                    'organization_id' => $organization->id,
                    'role_id' => $orgAdmin->id,
                ]);
            });

        Organization::factory()
            ->count(10)
            ->suspended()
            ->create()
            ->each(function (Organization $organization) use ($orgAdmin): void {
                User::factory()->create([
                    'organization_id' => $organization->id,
                    'role_id' => $orgAdmin->id,
                ]);
            });
    }
}
