<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketType;
use App\Models\User;
use Illuminate\Database\Seeder;

final class EventSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->organizationAdmin()->create();
        Event::factory()
            ->recycle($admin)
            ->recycle($admin->organization)
            ->has(TicketType::factory()->count(3))
            ->create();
    }
}
