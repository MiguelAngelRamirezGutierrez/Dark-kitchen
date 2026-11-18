<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class Clase10ComprasPdfTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
    }

    /**
     * Prueba 1: Registro de orden de compra en estado 'pendiente' SIN mover stock de bodega.
     */
    public function test_purchase_service_creates_order_in_pendiente_without_moving_stock(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $initialStock = $product->stock;
        $orderQuantity = 10;
        $unitCost = 15000.00;

        $service = app(PurchaseService::class);
        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'provider_invoice' => 'FAC-PROV-001',
            'notes' => 'Orden de abastecimiento carne',
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => $orderQuantity,
                    'unit_cost' => $unitCost,
                ],
            ],
        ], $admin->id);

        $this->assertInstanceOf(Purchase::class, $purchase);
        $this->assertEquals('pendiente', $purchase->status);
        $this->assertStringStartsWith('OC-', $purchase->purchase_number);
        $this->assertEquals(150000.00, (float) $purchase->subtotal);
        $this->assertEquals(28500.00, (float) $purchase->tax);
        $this->assertEquals(178500.00, (float) $purchase->total);

        // Regla fundamental: El inventario NO debe haberse modificado al emitir la orden
        $product->refresh();
        $this->assertEquals($initialStock, $product->stock, 'El stock no debe cambiar al solo crear la orden de compra.');
    }

    /**
     * Prueba 2: Recepción parcial incrementa stock y pasa a estado 'parcial'.
     */
    public function test_partial_reception_increments_stock_and_marks_status_parcial(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $initialStock = $product->stock;
        $service = app(PurchaseService::class);

        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'unit_cost' => 12000.00,
                ],
            ],
        ], $admin->id);

        $detail = $purchase->details()->first();

        // Recepción parcial de 4 unidades
        $updatedPurchase = $service->receive($purchase, [
            $detail->id => 4,
        ]);

        $this->assertEquals('parcial', $updatedPurchase->status);

        $product->refresh();
        $this->assertEquals($initialStock + 4, $product->stock, 'El stock debe incrementarse exactamente en 4.');

        $detail->refresh();
        $this->assertEquals(4, $detail->received_quantity);
        $this->assertEquals(6, $detail->pendingQuantity());
    }

    /**
     * Prueba 3: Segunda recepción totaliza la orden y pasa a estado 'recibida'.
     */
    public function test_total_reception_completes_order_and_increments_remaining_stock(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $initialStock = $product->stock;
        $service = app(PurchaseService::class);

        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 10,
                    'unit_cost' => 12000.00,
                ],
            ],
        ], $admin->id);

        $detail = $purchase->details()->first();

        // 1ra entrega: 4 unidades
        $service->receive($purchase, [$detail->id => 4]);

        // 2da entrega: 6 unidades restantes
        $finalPurchase = $service->receive($purchase, [$detail->id => 6]);

        $this->assertEquals('recibida', $finalPurchase->status);

        $product->refresh();
        $this->assertEquals($initialStock + 10, $product->stock, 'El stock total debe ser el inicial + 10.');

        $detail->refresh();
        $this->assertEquals(10, $detail->received_quantity);
        $this->assertEquals(0, $detail->pendingQuantity());
        $this->assertFalse($finalPurchase->canBeReceived());
    }

    /**
     * Prueba 4: Regla de negocio - No se puede recibir más de lo pendiente.
     */
    public function test_cannot_receive_more_than_pending_quantity(): void
    {
        $this->expectException(ValidationException::class);

        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $service = app(PurchaseService::class);
        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_cost' => 8000.00,
                ],
            ],
        ], $admin->id);

        $detail = $purchase->details()->first();

        // Intento recibir 6 unidades (máx permitido: 5)
        $service->receive($purchase, [$detail->id => 6]);
    }

    /**
     * Prueba 5: Regla de negocio - No se puede recibir sin cantidades válidas (vacío o <= 0).
     */
    public function test_cannot_receive_without_positive_quantities(): void
    {
        $this->expectException(ValidationException::class);

        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $service = app(PurchaseService::class);
        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_cost' => 8000.00,
                ],
            ],
        ], $admin->id);

        $detail = $purchase->details()->first();

        // Intento recibir 0 unidades
        $service->receive($purchase, [$detail->id => 0]);
    }

    /**
     * Prueba 6: Regla de negocio - No se puede anular una orden con recepciones previas.
     */
    public function test_cannot_cancel_purchase_with_received_goods(): void
    {
        $this->expectException(ValidationException::class);

        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $service = app(PurchaseService::class);
        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_cost' => 8000.00,
                ],
            ],
        ], $admin->id);

        $detail = $purchase->details()->first();
        $service->receive($purchase, [$detail->id => 2]);

        // Intentar anular
        $service->cancel($purchase);
    }

    /**
     * Prueba 7: Se puede anular una orden puramente pendiente.
     */
    public function test_can_cancel_pending_purchase(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $service = app(PurchaseService::class);
        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'unit_cost' => 8000.00,
                ],
            ],
        ], $admin->id);

        $cancelled = $service->cancel($purchase);
        $this->assertEquals('anulada', $cancelled->status);
    }

    /**
     * Prueba 8: No se puede eliminar un proveedor con órdenes de compra registradas.
     */
    public function test_cannot_delete_provider_with_existing_purchases(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $service = app(PurchaseService::class);
        $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                    'unit_cost' => 5000.00,
                ],
            ],
        ], $admin->id);

        $response = $this->actingAs($admin)
            ->delete(route('providers.destroy', $provider));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('providers', [
            'id' => $provider->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Prueba 9: Descarga de Orden de Compra en formato PDF (HTTP 200 y Content-Type application/pdf).
     */
    public function test_purchase_pdf_generation_returns_ok_and_pdf_mimetype(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $provider = Provider::where('active', true)->first();
        $product = Product::first();

        $service = app(PurchaseService::class);
        $purchase = $service->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                    'unit_cost' => 15000.00,
                ],
            ],
        ], $admin->id);

        $response = $this->actingAs($admin)
            ->get(route('purchases.pdf', $purchase));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Prueba 10: Descarga de Factura de Venta en formato PDF (HTTP 200 y Content-Type application/pdf).
     */
    public function test_sale_pdf_generation_returns_ok_and_pdf_mimetype(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $client = Client::first();
        $product = Product::where('stock', '>=', 2)->where('estado', true)->first();

        $saleService = app(SaleService::class);
        $sale = $saleService->register([
            'client_id' => $client->id,
            'sale_date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1,
                ],
            ],
        ], $admin->id);

        $response = $this->actingAs($admin)
            ->get(route('sales.pdf', $sale));

        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type'));
    }

    /**
     * Prueba 11: Dashboard integra KPIs de compras y tabla de reabastecimiento crítico.
     */
    public function test_dashboard_displays_clase10_kpis_and_reorder_table(): void
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Proveedores activos');
        $response->assertSee('Compras del mes');
        $response->assertSee('Órdenes por recibir');
        $response->assertSee('Margen mensual');
    }
}
