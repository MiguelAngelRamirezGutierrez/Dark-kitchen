<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar la caché de permisos de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos',
            'ver-categorias', 'crear-categorias', 'editar-categorias', 'eliminar-categorias',
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            'ver-pedidos', 'crear-pedidos', 'editar-pedidos', 'eliminar-pedidos',
            'ver-domiciliarios', 'crear-domiciliarios', 'editar-domiciliarios', 'eliminar-domiciliarios',
            'ver-reportes',
        ];

        // findOrCreate: si el permiso ya existe no lo duplica
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // syncPermissions: deja al rol exactamente con estos permisos
        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        Role::findOrCreate('vendedor')->syncPermissions([
            'ver-productos',
            'ver-clientes',
            'crear-clientes',
            'ver-pedidos',
            'crear-pedidos',
        ]);

        Role::findOrCreate('almacenista')->syncPermissions([
            'ver-productos',
            'crear-productos',
            'editar-productos',
            'ver-categorias',
        ]);

        // Crear o asignar los usuarios de prueba con sus roles
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['name' => 'Administrador QuickFood', 'password' => 'password']
        );
        $admin->syncRoles(['admin']);

        $vendedor = User::firstOrCreate(
            ['email' => 'vendedor@test.com'],
            ['name' => 'Vendedor Prueba', 'password' => 'password']
        );
        $vendedor->syncRoles(['vendedor']);

        $almacenista = User::firstOrCreate(
            ['email' => 'almacenista@test.com'],
            ['name' => 'Almacenista Prueba', 'password' => 'password']
        );
        $almacenista->syncRoles(['almacenista']);
    }
}
