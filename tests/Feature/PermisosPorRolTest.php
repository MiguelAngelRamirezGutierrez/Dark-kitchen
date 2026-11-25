<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Matriz de seguridad convertida en pruebas: cada fila es [rol, URL, código HTTP esperado].
 * Si alguien cambia un permiso por error, esta prueba lo detecta.
 */
class PermisosPorRolTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
    }

    public static function matriz(): array
    {
        return [
            ['admin',       '/configuracion',     200],
            ['admin',       '/auditoria',         200],
            ['vendedor',    '/sales/create',      200],
            ['vendedor',    '/purchases',         403],
            ['vendedor',    '/configuracion',     403],
            ['vendedor',    '/inventario/kardex', 403],
            ['almacenista', '/purchases/create',  200],
            ['almacenista', '/inventario/kardex', 200],
            ['almacenista', '/sales',             403],
            ['almacenista', '/auditoria',         403],
        ];
    }

    #[DataProvider('matriz')]
    public function test_acceso_por_rol(string $rol, string $url, int $esperado): void
    {
        $user = User::factory()->create()->assignRole($rol);
        $this->actingAs($user)->get($url)->assertStatus($esperado);
    }

    public function test_un_invitado_es_enviado_al_login(): void
    {
        $this->get('/sales')->assertRedirect('/login');
    }
}
