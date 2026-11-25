<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

/**
 * Clase 13: API REST con Sanctum y exportación/importación con Excel.
 */
class Clase13Test extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
    }

    private function token(string $email, ?string $role = null): string
    {
        return $this->postJson('/api/v1/login', array_filter([
            'email' => $email, 'password' => 'password', 'device_name' => 'pruebas', 'role' => $role,
        ]))->assertCreated()->json('token');
    }

    public function test_login_y_rol_activo_en_la_api(): void
    {
        $this->postJson('/api/v1/login', ['email' => 'admin@test.com', 'password' => 'mala', 'device_name' => 'x'])
            ->assertStatus(422)->assertJsonValidationErrors('email');

        // Usuario con dos roles: debe indicar el rol
        $this->postJson('/api/v1/login', ['email' => 'supervisor@test.com', 'password' => 'password', 'device_name' => 'x'])
            ->assertStatus(422)->assertJsonValidationErrors('role');

        $token = $this->token('supervisor@test.com', 'almacenista');
        $this->withToken($token)->getJson('/api/v1/me')->assertOk()
            ->assertJsonPath('rol_activo', 'almacenista')
            ->assertJsonFragment(['ver-reportes']); // permiso directo

        // Con el rol almacenista NO puede crear ventas por API
        $this->withToken($token)->postJson('/api/v1/sales', [])->assertForbidden();
    }

    public function test_productos_por_api(): void
    {
        $this->getJson('/api/v1/products')->assertUnauthorized();

        $token = $this->token('vendedor@test.com');
        $this->withToken($token)->getJson('/api/v1/products?search=Laptop')->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonStructure(['data' => [['id', 'nombre', 'precio', 'stock', 'disponible', 'categoria' => ['id', 'nombre']]], 'links', 'meta'])
            ->assertJsonPath('data.0.nombre', 'Laptop Lenovo')
            ->assertJsonMissingPath('data.0.deleted_at');

        $laptop = Product::where('name', 'Laptop Lenovo')->first();
        $this->withToken($token)->getJson("/api/v1/products/{$laptop->id}")->assertOk()->assertJsonPath('data.precio', 4500000);
        $this->withToken($token)->getJson('/api/v1/products/99999')->assertNotFound();
    }

    public function test_crear_venta_por_api_reutiliza_el_servicio(): void
    {
        $token = $this->token('vendedor@test.com');
        $laptop = Product::where('name', 'Laptop Lenovo')->first();

        // Validación (mismo StoreSaleRequest de la web)
        $this->withToken($token)->postJson('/api/v1/sales', ['client_id' => Client::first()->id, 'sale_date' => now()->toDateString(), 'items' => []])
            ->assertStatus(422)->assertJsonValidationErrors('items');

        // Regla de negocio del servicio (stock)
        $this->withToken($token)->postJson('/api/v1/sales', [
            'client_id' => Client::first()->id, 'sale_date' => now()->toDateString(),
            'items' => [['product_id' => $laptop->id, 'quantity' => 500]],
        ])->assertStatus(422)->assertJsonValidationErrors('items');

        $response = $this->withToken($token)->postJson('/api/v1/sales', [
            'client_id' => Client::first()->id, 'sale_date' => now()->toDateString(),
            'items' => [['product_id' => $laptop->id, 'quantity' => 2]],
        ])->assertCreated()
            ->assertJsonPath('data.total', 10710000)
            ->assertJsonPath('data.detalle.0.cantidad', 2);

        $this->assertSame(8, $laptop->fresh()->stock);
        $this->assertSame('venta', $laptop->movements()->latest('id')->value('concept'));

        $id = $response->json('data.id');
        $this->withToken($token)->getJson("/api/v1/sales/{$id}")->assertOk()->assertJsonPath('data.factura', $response->json('data.factura'));

        // Logout revoca el token
        $this->withToken($token)->postJson('/api/v1/logout')->assertOk();
        $this->assertSame(0, User::where('email', 'vendedor@test.com')->first()->tokens()->count());
    }

    public function test_exportar_a_excel(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $r = $this->actingAs($admin)->get('/products/exportar');
        $r->assertOk();
        $this->assertStringContainsString('spreadsheetml', $r->headers->get('content-type'));

        $this->actingAs($admin)->get('/sales/exportar?from=2026-01-01&to=2026-12-31')->assertOk();
        $this->actingAs($admin)->get('/sales/exportar?from=2026-12-31&to=2026-01-01')->assertSessionHasErrors('to');
        $this->actingAs($admin)->get('/products/plantilla')->assertOk();

        $this->actingAs(User::where('email', 'vendedor@test.com')->first())->get('/products/exportar')->assertForbidden();
        $this->actingAs($admin)->get('/products')->assertSee('Exportar a Excel')->assertSee('Importar desde Excel');
    }

    public function test_importar_productos_con_filas_invalidas(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        // Archivo real .xlsx: 2 filas válidas nuevas, 1 existente (actualiza precio), 2 inválidas
        $rows = new class implements FromArray, WithHeadings
        {
            public function headings(): array
            {
                return ['nombre', 'descripcion', 'precio', 'stock', 'categoria'];
            }

            public function array(): array
            {
                return [
                    ['Tablet Samsung A9', 'Tablet 8.7', 780000, 5, 'Computación'],
                    ['Cargador USB-C', null, 65000, 20, 'Accesorios'],
                    ['Laptop Lenovo', 'Actualizada', 4300000, 999, 'Computación'],
                    ['Producto sin precio', null, null, 1, 'Accesorios'],
                    ['Producto mala categoría', null, 1000, 1, 'Juguetes'],
                ];
            }
        };
        $path = tempnam(sys_get_temp_dir(), 'imp').'.xlsx';
        file_put_contents($path, Excel::raw($rows, ExcelType::XLSX));
        $file = new UploadedFile($path, 'productos.xlsx', null, null, true);

        $this->actingAs($admin)->post('/products/importar', ['file' => $file])
            ->assertRedirect('/products/importar')
            ->assertSessionHas('success', 'Importación terminada: 2 productos creados y 1 actualizados.');

        $failures = session('failures');
        $this->assertCount(2, $failures);
        $this->assertSame([5, 6], array_column($failures, 'fila'));
        // Los errores por fila se reportan en español
        $this->assertSame(['El precio es obligatorio.'], $failures[0]['errores']);
        $this->assertStringContainsString('no existe', $failures[1]['errores'][0]);

        $tablet = Product::where('name', 'Tablet Samsung A9')->first();
        $this->assertSame(5, $tablet->stock);
        $this->assertSame('inventario_inicial', $tablet->movements()->value('concept'));

        $laptop = Product::where('name', 'Laptop Lenovo')->first();
        $this->assertEquals(4300000, (float) $laptop->price);
        $this->assertSame(10, $laptop->stock, 'la importación nunca cambia el stock de un producto existente');
        $this->assertNull(Product::where('name', 'Producto mala categoría')->first());

        $this->actingAs($admin)->get('/products/importar')->assertOk();
        $this->actingAs($admin)->post('/products/importar', [
            'file' => UploadedFile::fake()->create('virus.exe', 10),
        ])->assertSessionHasErrors(['file' => 'El archivo debe ser .xlsx, .xls o .csv.']);
    }
}
