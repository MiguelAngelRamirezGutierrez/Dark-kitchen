<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pruebas de las REGLAS DE NEGOCIO de las ventas (Clase 15).
 * Estructura de cada prueba: Preparar (Arrange) → Ejecutar (Act) → Verificar (Assert).
 */
class VentasReglasTest extends TestCase
{
    use RefreshDatabase; // cada prueba empieza con la base de datos limpia

    private User $vendedor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(RolePermissionSeeder::class);
        $this->vendedor = User::factory()->create()->assignRole('vendedor');
    }

    private function vender(Product $product, int $cantidad, ?Client $client = null)
    {
        return $this->actingAs($this->vendedor)->post(route('sales.store'), [
            'client_id' => ($client ?? Client::factory()->create())->id,
            'sale_date' => now()->toDateString(),
            'items' => [['product_id' => $product->id, 'quantity' => $cantidad]],
        ]);
    }

    public function test_la_venta_descuenta_el_stock_y_calcula_el_iva(): void
    {
        // Preparar
        $product = Product::factory()->create(['price' => 100000, 'stock' => 10]);

        // Ejecutar
        $this->vender($product, 3)->assertRedirect();

        // Verificar
        $this->assertSame(7, $product->fresh()->stock);
        $sale = Sale::first();
        $this->assertNotNull($sale);
        $this->assertSame(300000.0, (float) $sale->subtotal);
        $this->assertSame(57000.0, (float) $sale->tax);
        $this->assertSame(357000.0, (float) $sale->total);
        $this->assertTrue(str_starts_with($sale->invoice_number, 'FV-'));
    }

    public function test_no_se_vende_mas_del_stock_disponible(): void
    {
        $product = Product::factory()->create(['stock' => 2]);
        $this->vender($product, 5)->assertSessionHasErrors('items');
        $this->assertSame(2, $product->fresh()->stock); // el stock no cambió
        $this->assertDatabaseCount('sales', 0); // y no quedó venta «huérfana» (rollback)
    }

    public function test_no_se_vende_a_un_cliente_inactivo(): void
    {
        $product = Product::factory()->create(['stock' => 5]);
        $this->vender($product, 1, Client::factory()->inactive()->create())
            ->assertSessionHasErrors('client_id');
    }

    public function test_anular_devuelve_el_stock(): void
    {
        $admin = User::factory()->create()->assignRole('admin');
        $product = Product::factory()->create(['stock' => 10]);
        $this->vender($product, 4);
        $sale = Sale::first();
        $this->actingAs($admin)->delete(route('sales.destroy', $sale))->assertRedirect();
        $this->assertSame(10, $product->fresh()->stock);
        $this->assertSoftDeleted('sales', ['id' => $sale->id, 'status' => 'anulada']);
    }

    public function test_el_vendedor_no_puede_anular_ventas(): void
    {
        $product = Product::factory()->create(['stock' => 10]);
        $this->vender($product, 1);
        $this->actingAs($this->vendedor)
            ->delete(route('sales.destroy', Sale::first()))
            ->assertForbidden();
    }
}
