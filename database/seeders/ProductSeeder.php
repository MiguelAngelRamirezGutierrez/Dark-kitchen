<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(InventoryService $inventory): void
    {
        $computacion = Category::firstOrCreate(['nombre' => 'Computación'], ['descripcion' => 'Equipos de cómputo', 'estado' => true]);
        $accesorios = Category::firstOrCreate(['nombre' => 'Accesorios'], ['descripcion' => 'Accesorios tecnológicos', 'estado' => true]);

        $products = [
            ['name' => 'Laptop Lenovo', 'price' => 4500000, 'stock' => 10, 'categoria_id' => $computacion->id, 'category_id' => $computacion->id],
            ['name' => 'Mouse Logitech', 'price' => 120000, 'stock' => 30, 'categoria_id' => $accesorios->id, 'category_id' => $accesorios->id],
            ['name' => 'Teclado Mecánico', 'price' => 250000, 'stock' => 15, 'categoria_id' => $accesorios->id, 'category_id' => $accesorios->id],
            ['name' => 'Monitor Samsung', 'price' => 850000, 'stock' => 4, 'categoria_id' => $computacion->id, 'category_id' => $computacion->id],
            ['name' => 'Diadema HyperX', 'price' => 320000, 'stock' => 8, 'categoria_id' => $accesorios->id, 'category_id' => $accesorios->id],
        ];

        foreach ($products as $product) {
            $model = Product::firstOrCreate(['nombre' => $product['name']], $product + ['active' => true, 'estado' => true]);
            $inventory->opening($model);
        }
    }
}
