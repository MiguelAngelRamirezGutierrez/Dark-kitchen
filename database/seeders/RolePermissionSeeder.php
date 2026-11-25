<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * Crea (o actualiza) permisos y roles. Se puede ejecutar varias veces sin duplicar registros.
     *
     * IMPORTANTE: define el estado INICIAL de los roles. Si después se cambian permisos desde
     * Configuración → Roles, volver a ejecutar este seeder los devuelve a lo que dice aquí.
     */
    public function run(): void
    {
        // Limpiar la caché de permisos de Spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Productos
            'ver-productos', 'crear-productos', 'editar-productos', 'eliminar-productos',
            // Categorías
            'ver-categorias', 'crear-categorias', 'editar-categorias', 'eliminar-categorias',
            // Clientes (Clase 9)
            'ver-clientes', 'crear-clientes', 'editar-clientes', 'eliminar-clientes',
            // Ventas (Clase 9)
            'ver-ventas', 'crear-ventas', 'editar-ventas', 'eliminar-ventas',
            // Proveedores (Clase 10)
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores', 'eliminar-proveedores',
            // Compras (Clase 10: se agrega recibir-compras)
            'ver-compras', 'crear-compras', 'editar-compras', 'eliminar-compras', 'recibir-compras',
            // Pedidos (ERP QuickFood / Dark Kitchen)
            'ver-pedidos', 'crear-pedidos', 'editar-pedidos', 'eliminar-pedidos', 'cambiar-estado-pedidos',
            // Domiciliarios
            'ver-domiciliarios', 'crear-domiciliarios', 'editar-domiciliarios', 'eliminar-domiciliarios',
            // Papelera y Reportes
            'ver-papelera', 'ver-reportes',

            // Configuración de seguridad (guía adicional de la Clase 10)
            'ver-usuarios', 'crear-usuarios', 'editar-usuarios',
            'ver-roles', 'crear-roles', 'editar-roles', 'eliminar-roles',
            'ver-permisos', 'crear-permisos', 'eliminar-permisos',

            // Inventario (kardex) y auditoría (Clase 12)
            'ver-inventario', 'ajustar-inventario',
            'ver-auditoria',

            // Excel (Clase 13)
            'exportar-productos', 'importar-productos', 'exportar-ventas',
        ];

        // findOrCreate: si el permiso ya existe no lo duplica
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // El administrador siempre tiene TODOS los permisos
        Role::findOrCreate('admin')->syncPermissions(Permission::all());

        // Rol Vendedor
        Role::findOrCreate('vendedor')->syncPermissions([
            'ver-productos',
            'ver-clientes', 'crear-clientes', 'editar-clientes',
            'ver-ventas', 'crear-ventas',
            'ver-pedidos', 'crear-pedidos', 'cambiar-estado-pedidos',
            'exportar-ventas',
        ]);

        // Rol Almacenista
        Role::findOrCreate('almacenista')->syncPermissions([
            'ver-productos', 'crear-productos', 'editar-productos',
            'ver-categorias',
            'ver-proveedores', 'crear-proveedores', 'editar-proveedores',
            'ver-compras', 'crear-compras', 'recibir-compras',
            'ver-papelera',
            'ver-inventario', 'ajustar-inventario',
            'exportar-productos', 'importar-productos',
        ]);
    }
}
