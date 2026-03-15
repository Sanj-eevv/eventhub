<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\OrganizationStatus;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Database\Seeder;

final class EventSeeder extends Seeder
{
    public function run(): void
    {
        Organization::query()
            ->where('status', OrganizationStatus::Approved)
            ->with('users')
            ->get()
            ->each(function (Organization $organization): void {
                $user = $organization->users->first();

                Event::factory()->count(5)->create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                ]);

                Event::factory()->count(5)->published()->create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                ]);

                Event::factory()->count(2)->cancelled()->create([
                    'organization_id' => $organization->id,
                    'user_id' => $user->id,
                ]);
            });
    }
}
