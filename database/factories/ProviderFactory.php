<?php

namespace Database\Factories;

use App\Models\Provider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Provider>
 */
class ProviderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_type' => 'NIT',
            'document_number' => (string) fake()->unique()->numberBetween(800000000, 999999999),
            'name' => fake()->company().' SAS',
            'contact_name' => fake()->name(),
            'email' => fake()->unique()->companyEmail(),
            'phone' => '60'.fake()->numerify('########'),
            'address' => fake()->streetAddress(),
            'active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
