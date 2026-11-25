<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * Fábrica de productos para pruebas (Clase 15).
 * Crea un producto con categoría y stock válidos en una sola línea.
 *
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        // Reutiliza la primera categoría existente o crea una al vuelo
        $categoriaId = Category::first()?->id
            ?? DB::table('categorias')->insertGetId([
                'nombre' => 'General',
                'descripcion' => null,
                'estado' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        return [
            'categoria_id' => $categoriaId,
            'nombre' => fake()->words(3, true),
            'descripcion' => fake()->sentence(),
            'precio' => fake()->randomFloat(2, 10000, 500000),
            'stock' => fake()->numberBetween(10, 100),
            'estado' => true,
        ];
    }

    // Producto con stock específico
    public function withStock(int $stock): static
    {
        return $this->state(fn () => ['stock' => $stock]);
    }

    // Producto inactivo
    public function inactive(): static
    {
        return $this->state(fn () => ['estado' => false]);
    }
}
