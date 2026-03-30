<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TicketType>
 */
final class TicketTypeFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        $saleStartsAt = $this->faker->dateTimeBetween('-1 month', '+1 month');
        $saleEndsAt = $this->faker->dateTimeBetween($saleStartsAt, '+3 months');

        return [
            'event_id' => Event::factory(),
            'name' => $name,
            'description' => $this->faker->sentence(),
            'slug' => Str::slug($name),
            'price' => $this->faker->numberBetween(500, 20000),
            'capacity' => $this->faker->numberBetween(10, 500),
            'max_per_user' => null,
            'sort_order' => 0,
            'sale_starts_at' => $saleStartsAt,
            'sale_ends_at' => $saleEndsAt,
        ];
    }
}
