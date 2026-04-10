<?php

declare(strict_types=1);

namespace Tests\Traits;

use App\Models\Organization;
use App\Models\Role;
use App\Models\User;

trait CreatesUsers
{
    protected function createSuperAdmin(): User
    {
        return User::factory()->create([
            'role_id' => Role::superAdminRole()->id,
            'email_verified_at' => now(),
        ]);
    }

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role_id' => Role::adminRole()->id,
            'email_verified_at' => now(),
        ]);
    }

    protected function createOrganizationAdmin(?Organization $organization = null): User
    {
        $organization ??= Organization::factory()->approved()->create();

        return User::factory()->create([
            'role_id' => Role::organizationAdminRole()->id,
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);
    }

    protected function createUser(): User
    {
        return User::factory()->create([
            'role_id' => Role::userRole()->id,
            'email_verified_at' => now(),
        ]);
    }
}
