<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrganizationStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
final class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->company();
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => fake()->paragraph(),
            'contact_address' => fake()->address(),
            'contact_email' => fake()->unique()->companyEmail(),
            'status' => OrganizationStatus::Pending,
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => OrganizationStatus::Active,
            'verified_at' => now(),
        ]);
    }
}
