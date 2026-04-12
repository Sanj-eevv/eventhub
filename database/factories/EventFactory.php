<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
final class EventFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->sentence(4, false);
        $startsAt = CarbonImmutable::instance($this->faker->dateTimeBetween('now', '+6 months'));

        return [
            'organization_id' => Organization::factory()->approved(),
            'user_id' => fn (array $attributes) => User::factory()
                ->state(['organization_id' => $attributes['organization_id']]),
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'starts_at' => $startsAt,
            'ends_at' => CarbonImmutable::instance($this->faker->dateTimeBetween($startsAt, '+7 months')),
            'status' => EventStatus::Draft,
            'timezone' => 'UTC',
            'venue_name' => $this->faker->company(),
            'address' => $this->faker->streetAddress(),
            'zip' => $this->faker->postcode(),
            'map_url' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => EventStatus::Published,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(function (): array {
            $startsAt = CarbonImmutable::instance($this->faker->dateTimeBetween('-6 months', '-1 month'));

            return [
                'status' => EventStatus::Cancelled,
                'starts_at' => $startsAt,
                'ends_at' => CarbonImmutable::instance($this->faker->dateTimeBetween($startsAt, 'now')),
            ];
        });
    }
}
