<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Fábrica de clientes: crea datos de prueba realistas en una sola línea.
 * Uso: Client::factory()->create(); Client::factory()->count(10)->create();
 *
 * @extends Factory<Client>
 */
class ClientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'document_type' => 'CC',
            'document_number' => (string) fake()->unique()->numberBetween(10000000, 1999999999),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '3'.fake()->numerify('#########'),
            'address' => fake()->streetAddress(),
            'active' => true,
        ];
    }

    // Estado: cliente inactivo → Client::factory()->inactive()->create()
    public function inactive(): static
    {
        return $this->state(fn () => ['active' => false]);
    }
}
