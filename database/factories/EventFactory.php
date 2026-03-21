<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
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
        $startsAt = $this->faker->dateTimeBetween('now', '+6 months');

        return [
            'organization_id' => Organization::factory()->approved(),
            'user_id' => fn (array $attributes) => User::factory()->create([
                'organization_id' => $attributes['organization_id'],
            ])->id,
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph,
            'starts_at' => $startsAt,
            'ends_at' => $this->faker->dateTimeBetween($startsAt, '+7 months'),
            'status' => EventStatus::Draft,
            'timezone' => 'UTC',
            'location' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'status' => EventStatus::Published,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(function (): array {
            $startsAt = $this->faker->dateTimeBetween('-6 months', '-1 month');

            return [
                'status' => EventStatus::Cancelled,
                'starts_at' => $startsAt,
                'ends_at' => $this->faker->dateTimeBetween($startsAt, 'now'),
            ];
        });
    }
}
