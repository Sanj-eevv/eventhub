<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\OrganizationStatus;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
final class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $title = $this->faker->company();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(),
            'contact_address' => $this->faker->address(),
            'contact_email' => $this->faker->unique()->companyEmail(),
            'status' => OrganizationStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (): array => [
            'status' => OrganizationStatus::Approved,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (): array => [
            'status' => OrganizationStatus::Suspended,
        ]);
    }
}
