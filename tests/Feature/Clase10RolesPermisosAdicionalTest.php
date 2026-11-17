<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AccessControlService;
use App\Support\PermissionCatalog;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Clase10RolesPermisosAdicionalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        User::query()->update(['active' => true]);
    }

    /**
     * Prueba 1: Usuario con un solo rol tiene ese rol como activo automáticamente.
     */
    public function test_user_with_single_role_has_that_role_as_active(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->first();

        $this->assertFalse($vendedor->hasMultipleRoles());
        $this->assertNotNull($vendedor->activeRole());
        $this->assertEquals('vendedor', $vendedor->activeRole()->name);
    }

    /**
     * Prueba 2: Usuario con múltiples roles es redirigido a seleccionar su rol de trabajo.
     */
    public function test_user_with_multiple_roles_requires_selecting_active_role(): void
    {
        $supervisor = User::where('email', 'supervisor@test.com')->first();

        $this->assertTrue($supervisor->hasMultipleRoles());

        // Al ingresar sin sesión de rol activo, el middleware redirige a active-role.edit
        $response = $this->actingAs($supervisor)->get(route('dashboard'));
        $response->assertRedirect(route('active-role.edit'));
    }

    /**
     * Prueba 3: Selección y conmutación de rol activo en sesión.
     */
    public function test_user_can_select_and_switch_active_role(): void
    {
        $supervisor = User::where('email', 'supervisor@test.com')->first();

        // 1. Selecciona rol 'vendedor'
        $response = $this->actingAs($supervisor)
            ->put(route('active-role.update'), [
                'role' => 'vendedor',
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('vendedor', session('active_role'));

        // 2. Conmuta a rol 'almacenista'
        $response = $this->actingAs($supervisor)
            ->put(route('active-role.update'), [
                'role' => 'almacenista',
            ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertEquals('almacenista', session('active_role'));
    }

    /**
     * Prueba 4: Segregación de funciones - Restricción estricta de permisos según el rol activo.
     */
    public function test_segregation_of_duties_active_role_restricts_permissions(): void
    {
        $supervisor = User::where('email', 'supervisor@test.com')->first();

        // Modo Vendedor: puede ver ventas, pero compras le responde 403
        $this->withSession(['active_role' => 'vendedor'])
            ->actingAs($supervisor);

        $this->assertTrue($supervisor->can('ver-ventas'));
        $this->assertFalse($supervisor->can('ver-compras'));

        $this->get(route('sales.index'))->assertOk();
        $this->get(route('purchases.index'))->assertForbidden();

        // Modo Almacenista: puede ver compras, pero ventas le responde 403
        $this->withSession(['active_role' => 'almacenista'])
            ->actingAs($supervisor);

        $this->assertFalse($supervisor->can('ver-ventas'));
        $this->assertTrue($supervisor->can('ver-compras'));

        $this->get(route('purchases.index'))->assertOk();
        $this->get(route('sales.index'))->assertForbidden();
    }

    /**
     * Prueba 5: Permisos directos aplican con cualquier rol activo (excepciones personales).
     */
    public function test_direct_permission_applies_regardless_of_active_role(): void
    {
        $supervisor = User::where('email', 'supervisor@test.com')->first();

        // supervisor tiene el permiso directo 'ver-reportes'
        $this->withSession(['active_role' => 'vendedor'])->actingAs($supervisor);
        $this->assertTrue($supervisor->can('ver-reportes'));

        $this->withSession(['active_role' => 'almacenista'])->actingAs($supervisor);
        $this->assertTrue($supervisor->can('ver-reportes'));
    }

    /**
     * Prueba 6: Usuario inactivo no puede iniciar sesión.
     */
    public function test_inactive_user_cannot_login(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->first();
        $vendedor->update(['active' => false]);

        $response = $this->post(route('login'), [
            'email' => 'vendedor@test.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Prueba 7: Usuario desactivado mientras tenía la sesión abierta es expulsado por el middleware.
     */
    public function test_inactive_user_with_open_session_is_expelled_by_middleware(): void
    {
        $vendedor = User::where('email', 'vendedor@test.com')->first();
        $this->actingAs($vendedor);

        // Desactivación concurrente por un administrador
        $vendedor->update(['active' => false]);

        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /**
     * Prueba 8: Nadie puede desactivar su propio usuario.
     */
    public function test_admin_cannot_deactivate_himself(): void
    {
        $this->expectException(ValidationException::class);

        $admin = User::where('email', 'admin@test.com')->first();
        $service = app(AccessControlService::class);

        $service->toggleActive($admin, $admin);
    }

    /**
     * Prueba 9: Siempre debe quedar al menos un administrador activo en el sistema.
     */
    public function test_cannot_remove_last_active_admin(): void
    {
        // Dejamos un único admin activo
        User::where('email', '!=', 'admin@test.com')->each(function ($u) {
            $u->removeRole('admin');
        });

        $admin = User::where('email', 'admin@test.com')->first();
        $otherUser = User::where('email', 'vendedor@test.com')->first();
        $service = app(AccessControlService::class);

        $this->expectException(ValidationException::class);

        // Intento quitarle el rol admin al único admin
        $service->saveUser($admin, [
            'name' => $admin->name,
            'email' => $admin->email,
            'active' => true,
            'roles' => ['vendedor'],
        ], $otherUser);
    }

    /**
     * Prueba 10: El rol administrador conserva siempre todos los permisos y no se puede eliminar.
     */
    public function test_super_role_admin_cannot_be_deleted(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $roleAdmin = Role::findByName('admin');

        $response = $this->actingAs($admin)
            ->delete(route('settings.roles.destroy', $roleAdmin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'admin']);
    }

    /**
     * Prueba 11: No se puede eliminar un rol que tenga usuarios asignados.
     */
    public function test_role_with_users_cannot_be_deleted(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $roleVendedor = Role::findByName('vendedor');

        $response = $this->actingAs($admin)
            ->delete(route('settings.roles.destroy', $roleVendedor));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('roles', ['name' => 'vendedor']);
    }

    /**
     * Prueba 12: No se puede eliminar un permiso que esté en uso por roles o usuarios.
     */
    public function test_permission_in_use_cannot_be_deleted(): void
    {
        $this->expectException(ValidationException::class);

        $permission = Permission::findByName('ver-productos');
        $service = app(AccessControlService::class);

        $service->deletePermission($permission);
    }

    /**
     * Prueba 13: La actualización de la matriz guarda únicamente los permisos visibles del filtro.
     */
    public function test_permission_matrix_updates_only_visible_permissions(): void
    {
        $vendedor = Role::findByName('vendedor');
        $initialPermissions = $vendedor->permissions->pluck('name')->all();

        $this->assertContains('ver-productos', $initialPermissions);

        // Simulamos formulario donde se filtró por 'compras'
        $response = $this->actingAs(User::where('email', 'admin@test.com')->first())
            ->put(route('settings.permissions.matrix'), [
                'visible' => ['ver-compras', 'crear-compras'],
                'matrix' => [
                    $vendedor->id => ['ver-compras'], // le agregamos ver-compras
                ],
            ]);

        $response->assertSessionHas('success');

        $vendedor->refresh();
        $updated = $vendedor->permissions->pluck('name')->all();

        // Debe tener ver-compras Y conservar ver-productos
        $this->assertContains('ver-compras', $updated);
        $this->assertContains('ver-productos', $updated, 'No debe haber borrado permisos de otros módulos');
    }

    /**
     * Prueba 14: Acceso restringido al módulo de configuración (solo con permisos correspondientes).
     */
    public function test_settings_pages_accessible_only_by_authorized_users(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $vendedor = User::where('email', 'vendedor@test.com')->first();

        // Admin accede sin problemas
        $this->actingAs($admin)->get(route('settings.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.users.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.roles.index'))->assertOk();
        $this->actingAs($admin)->get(route('settings.permissions.index'))->assertOk();

        // Vendedor sin permisos recibe 403
        $this->actingAs($vendedor)->get(route('settings.index'))->assertForbidden();
        $this->actingAs($vendedor)->get(route('settings.users.index'))->assertForbidden();
        $this->actingAs($vendedor)->get(route('settings.roles.index'))->assertForbidden();
        $this->actingAs($vendedor)->get(route('settings.permissions.index'))->assertForbidden();
    }
}
