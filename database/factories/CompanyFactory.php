<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyMembership;
use App\Models\Industry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'industry_id' => Industry::factory(),
            'contact_email' => fake()->unique()->safeEmail(),
        ];
    }

    /**
     * Give the company an Owner, either the given user or a new one.
     */
    public function withOwner(?User $owner = null): static
    {
        return $this->afterCreating(function (Company $company) use ($owner): void {
            CompanyMembership::factory()->owner()->for($company)->for($owner ?? User::factory())->create();
        });
    }
}
