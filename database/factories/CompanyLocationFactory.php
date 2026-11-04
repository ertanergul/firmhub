<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CompanyLocation>
 */
class CompanyLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'address_line1' => fake()->streetAddress(),
            'city' => fake()->city(),
            'country_code' => 'TR',
            'is_primary' => false,
        ];
    }

    /**
     * Make the location the company's primary location.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
