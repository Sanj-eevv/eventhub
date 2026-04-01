<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

final class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            [
                'key' => 'ticket_reservation_minutes',
                'value' => '5',
                'description' => 'How long (in minutes) a ticket reservation is held before it expires.',
            ],
            [
                'key' => 'cancellation_cutoff_hours',
                'value' => '24',
                'description' => 'How many hours before the event start a paid order can still be cancelled.',
            ],
            [
                'key' => 'refund_percentage',
                'value' => '100',
                'description' => 'Percentage of the order total refunded when a cancellation is processed.',
            ],
        ];

        Setting::upsert($defaults, ['key'], ['description']);
    }
}
