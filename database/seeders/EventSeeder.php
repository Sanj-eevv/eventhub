<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class EventSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::factory()->approved()->create([
            'title' => 'CatWalk',
            'slug' => 'cate-walk',
            'description' => 'We organise premier technology conferences across Nepal.',
            'contact_address' => 'Itahari, Sunsari, Nepal',
            'contact_email' => 'info@catwalk.test',
        ]);

        $admin = User::factory()->organizationAdmin()->create([
            'name' => 'Sandesh Tamang',
            'email' => 'organizer@test.test',
            'organization_id' => $organization->id,
        ]);

        $timezone = 'Asia/Kathmandu';
        $startsAt = CarbonImmutable::now($timezone)->addDays(5)->setTime(9, 0)->utc();
        $endsAt = $startsAt->addDay()->setTime(18, 0);
        $saleStartsAt = $startsAt->subDays(2)->setTime(9, 0);
        $saleOpenSince = CarbonImmutable::now($timezone)->subDay()->setTime(9, 0)->utc();

        $event = Event::query()->create([
            'organization_id' => $organization->id,
            'user_id' => $admin->id,
            'title' => 'Laravel Live 2026',
            'slug' => 'laravel-live-2026',
            'description' => 'Two days of deep-dive Laravel and PHP talks, workshops, and networking with the best engineers in the ecosystem. Join us at the Javits Center in New York City.',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'timezone' => $timezone,
            'status' => EventStatus::Published,
            'venue_name' => 'Itahari Mall',
            'address' => '429 11th Street, Sangeet Chowk, Itahari',
            'zip' => '54321',
            'map_url' => 'https://maps.google.com/?q=Sangeet+Chowk+Itahari+Nepal',
        ]);

        $ticketTypes = [
            [
                'name' => 'Early Bird',
                'slug' => 'early-bird',
                'description' => 'Discounted rate for early registrants. Limited to the first 100 tickets.',
                'price' => 19900,
                'capacity' => 100,
                'max_per_user' => 2,
                'sort_order' => 0,
                'sale_starts_at' => $saleStartsAt,
                'sale_ends_at' => $startsAt->subDays(3)->setTime(23, 59),
            ],
            [
                'name' => 'General Admission',
                'slug' => 'general-admission',
                'description' => 'Full two-day conference access including all talks, workshops, and the evening social.',
                'price' => 34900,
                'capacity' => 500,
                'max_per_user' => 4,
                'sort_order' => 1,
                'sale_starts_at' => $saleOpenSince,
                'sale_ends_at' => null,
            ],
            [
                'name' => 'VIP',
                'slug' => Str::slug('VIP-laravel-live-2026'),
                'description' => 'Premium access with reserved front-row seating, speaker dinner on Friday night, and an exclusive workshop on Saturday morning.',
                'price' => 79900,
                'capacity' => 30,
                'max_per_user' => 1,
                'sort_order' => 2,
                'sale_starts_at' => $saleStartsAt,
                'sale_ends_at' => $startsAt->subDay()->setTime(23, 59),
            ],
        ];

        foreach ($ticketTypes as $ticketType) {
            $event->ticketTypes()->create(array_merge($ticketType, ['event_id' => $event->id]));
        }
    }
}
