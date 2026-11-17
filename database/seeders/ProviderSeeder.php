<?php

namespace Database\Seeders;

use App\Models\Provider;
use Illuminate\Database\Seeder;

class ProviderSeeder extends Seeder
{
    public function run(): void
    {
        $providers = [
            [
                'document_type' => 'NIT',
                'document_number' => '900111222',
                'name' => 'Carnes & Cortes Madurados del Valle SAS',
                'contact_name' => 'Laura Ríos',
                'email' => 'ventas@carnesdelvalle.com',
                'phone' => '6012345678',
                'address' => 'Cartago, Calle 14 #4-20',
                'active' => true,
            ],
            [
                'document_type' => 'NIT',
                'document_number' => '900333444',
                'name' => 'Panadería Artesanal & Brioche Gourmet SAS',
                'contact_name' => 'Pedro Salazar',
                'email' => 'pedidos@panaderiagourmet.com',
                'phone' => '6044567890',
                'address' => 'Pereira, Cra 7 #18-35',
                'active' => true,
            ],
            [
                'document_type' => 'NIT',
                'document_number' => '900555666',
                'name' => 'Distribuidora Central de Bebidas & Salsas',
                'contact_name' => 'Diana Mejía',
                'email' => 'comercial@centralbebidas.com',
                'phone' => '6027654321',
                'address' => 'Cali, Av. 6N #23-45',
                'active' => true,
            ],
            [
                'document_type' => 'CC',
                'document_number' => '1094555666',
                'name' => 'Andrés Cardona (Huerta & Verduras Frescas)',
                'contact_name' => 'Andrés Cardona',
                'email' => 'andres.cardona@test.com',
                'phone' => '3135556677',
                'address' => 'Cartago, Vereda Zaragoza Km 2',
                'active' => true,
            ],
        ];

        // firstOrCreate: si el documento ya existe no lo duplica (se puede ejecutar varias veces)
        foreach ($providers as $provider) {
            Provider::firstOrCreate(['document_number' => $provider['document_number']], $provider);
        }
    }
}
