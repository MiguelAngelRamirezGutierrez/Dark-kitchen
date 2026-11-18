<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Client;
use App\Models\InventoryMovement;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\User;
use App\Services\AccessControlService;
use App\Services\InventoryService;
use App\Services\PurchaseService;
use App\Services\SaleService;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\TestUserSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Validation\ValidationException;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Clase12AuditoriaKardexTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;
    protected User $almacenista;
    protected User $vendedor;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        $this->seed(TestUserSeeder::class);

        User::query()->update(['active' => true]);

        $this->admin = User::where('email', 'admin@test.com')->first();
        $this->almacenista = User::where('email', 'almacenista@test.com')->first();
        $this->vendedor = User::where('email', 'vendedor@test.com')->first();

        $this->category = Category::firstOrCreate(
            ['nombre' => 'Hamburguesas'],
            ['descripcion' => 'Hamburguesas artesanales', 'estado' => true]
        );
    }

    public function test_product_creation_registers_initial_stock_in_kardex(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('products.store'), [
            'name' => 'Burger BBQ',
            'nombre' => 'Burger BBQ',
            'price' => 25000,
            'precio' => 25000,
            'stock' => 15,
            'category_id' => $this->category->id,
            'categoria_id' => $this->category->id,
            'active' => 1,
            'estado' => 1,
        ]);

        $response->assertRedirect(route('products.index'));

        $product = Product::where('nombre', 'Burger BBQ')->firstOrFail();
        $this->assertEquals(15, $product->stock);

        $this->assertDatabaseHas('inventory_movements', [
            'product_id' => $product->id,
            'type' => 'entrada',
            'concept' => 'inventario_inicial',
            'quantity' => 15,
            'balance' => 15,
        ]);

        $movement = $product->movements()->first();
        $this->assertNotNull($movement);
        $this->assertEquals('Inventario inicial', $movement->conceptLabel());
    }

    public function test_product_update_excludes_stock_from_manual_editing_and_audits_changes(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'nombre' => 'Papas Criollas',
            'name' => 'Papas Criollas',
            'precio' => 10000,
            'price' => 10000,
            'stock' => 20,
            'categoria_id' => $this->category->id,
            'category_id' => $this->category->id,
            'estado' => true,
            'active' => true,
        ]);

        app(InventoryService::class)->opening($product);

        $response = $this->put(route('products.update', $product), [
            'nombre' => 'Papas Criollas Especiales',
            'name' => 'Papas Criollas Especiales',
            'precio' => 14000,
            'price' => 14000,
            'stock' => 999, // intento de cambiar stock a mano
            'categoria_id' => $this->category->id,
            'category_id' => $this->category->id,
            'estado' => 1,
            'active' => 1,
        ]);

        $response->assertRedirect(route('products.index'));

        $product->refresh();
        $this->assertEquals(20, $product->stock, 'El stock no debió cambiar mediante edición del producto.');
        $this->assertEquals(14000, (float) $product->price);

        // Verificar que en activity_log se auditó el cambio de precio y nombre, pero NO el stock
        $activity = Activity::where('subject_type', $product->getMorphClass())
            ->where('subject_id', $product->id)
            ->where('event', 'updated')
            ->latest('id')
            ->first();

        $this->assertNotNull($activity, 'Debe existir registro en activity_log para la actualización del producto.');
        $this->assertEquals('productos', $activity->log_name);

        $changes = $activity->attribute_changes;
        $attributes = is_array($changes) ? ($changes['attributes'] ?? []) : ($changes->get('attributes', []) ?? []);
        $this->assertArrayNotHasKey('stock', $attributes, 'El stock nunca debe auditarse en activity_log.');
    }

    public function test_sale_registers_kardex_exit_and_updates_balance(): void
    {
        $this->actingAs($this->vendedor);

        $product = Product::create([
            'nombre' => 'Combo Familiar',
            'name' => 'Combo Familiar',
            'precio' => 50000,
            'price' => 50000,
            'stock' => 10,
            'categoria_id' => $this->category->id,
            'category_id' => $this->category->id,
            'estado' => true,
            'active' => true,
        ]);

        app(InventoryService::class)->opening($product);

        $client = Client::create([
            'document_type' => 'CC',
            'document_number' => '100200300',
            'name' => 'Juan Perez',
            'active' => true,
        ]);

        $saleService = app(SaleService::class);
        $sale = $saleService->register([
            'client_id' => $client->id,
            'sale_date' => now()->toDateString(),
            'notes' => 'Venta de prueba kardex',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
        ], $this->vendedor->id);

        $product->refresh();
        $this->assertEquals(7, $product->stock);

        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('concept', 'venta')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals('salida', $movement->type);
        $this->assertEquals(3, $movement->quantity);
        $this->assertEquals(7, $movement->balance);
        $this->assertEquals($sale->getMorphClass(), $movement->reference_type);
        $this->assertEquals($sale->id, $movement->reference_id);
        $this->assertEquals($sale->invoice_number, $movement->referenceLabel());
    }

    public function test_annuling_sale_restores_stock_with_kardex_entry(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'nombre' => 'Bebida Limón',
            'name' => 'Bebida Limón',
            'precio' => 6000,
            'price' => 6000,
            'stock' => 12,
            'categoria_id' => $this->category->id,
            'category_id' => $this->category->id,
            'estado' => true,
            'active' => true,
        ]);

        app(InventoryService::class)->opening($product);

        $client = Client::create([
            'document_type' => 'CC',
            'document_number' => '100200301',
            'name' => 'Maria Lopez',
            'active' => true,
        ]);

        $sale = app(SaleService::class)->register([
            'client_id' => $client->id,
            'sale_date' => now()->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 4],
            ],
        ], $this->admin->id);

        $this->assertEquals(8, $product->fresh()->stock);

        // Anular la venta
        $response = $this->delete(route('sales.destroy', $sale));
        $response->assertRedirect(route('sales.index'));

        $product->refresh();
        $this->assertEquals(12, $product->stock, 'El stock debe volver a 12 tras la anulación.');

        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('concept', 'anulacion_venta')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals('entrada', $movement->type);
        $this->assertEquals(4, $movement->quantity);
        $this->assertEquals(12, $movement->balance);
        $this->assertEquals($sale->invoice_number, $movement->referenceLabel());
    }

    public function test_purchase_reception_creates_compra_kardex_entry_with_cost(): void
    {
        $this->actingAs($this->almacenista);

        $product = Product::create([
            'nombre' => 'Pan Brioche x10',
            'name' => 'Pan Brioche x10',
            'precio' => 15000,
            'price' => 15000,
            'stock' => 5,
            'categoria_id' => $this->category->id,
            'category_id' => $this->category->id,
            'estado' => true,
            'active' => true,
        ]);

        app(InventoryService::class)->opening($product);

        $provider = Provider::create([
            'document_type' => 'NIT',
            'document_number' => '900999888',
            'name' => 'Panadería Central',
            'active' => true,
        ]);

        $purchaseService = app(PurchaseService::class);
        $purchase = $purchaseService->register([
            'provider_id' => $provider->id,
            'purchase_date' => now()->toDateString(),
            'items' => [
                ['product_id' => $product->id, 'quantity' => 10, 'unit_cost' => 8500],
            ],
        ], $this->almacenista->id);

        $this->assertEquals(5, $product->fresh()->stock, 'La orden pendiente no debe mover el inventario.');

        $detail = $purchase->details()->first();
        $purchaseService->receive($purchase, [$detail->id => 6], 'FAC-123');

        $product->refresh();
        $this->assertEquals(11, $product->stock, 'La recepción de 6 unidades debe subir el stock de 5 a 11.');

        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('concept', 'compra')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals('entrada', $movement->type);
        $this->assertEquals(6, $movement->quantity);
        $this->assertEquals(11, $movement->balance);
        $this->assertEquals(8500.00, (float) $movement->unit_cost);
        $this->assertEquals($purchase->purchase_number, $movement->referenceLabel());
    }

    public function test_inventory_adjustment_increases_and_decreases_stock_with_reason(): void
    {
        $this->actingAs($this->admin);

        $product = Product::create([
            'nombre' => 'Queso Mozzarella Barra',
            'name' => 'Queso Mozzarella Barra',
            'precio' => 45000,
            'price' => 45000,
            'stock' => 10,
            'categoria_id' => $this->category->id,
            'category_id' => $this->category->id,
            'estado' => true,
            'active' => true,
        ]);

        app(InventoryService::class)->opening($product);

        // 1. Ajuste negativo (conteo menor al stock: 8)
        $response = $this->post(route('inventory.adjust.store', $product), [
            'counted_stock' => 8,
            'reason' => 'Conteo de cierre de mes: 2 barras vencidas descartadas',
        ]);

        $response->assertRedirect(route('inventory.kardex', ['product' => $product->id]));
        $product->refresh();
        $this->assertEquals(8, $product->stock);

        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('concept', 'ajuste')
            ->where('type', 'salida')
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals(2, $movement->quantity);
        $this->assertEquals(8, $movement->balance);
        $this->assertStringContainsString('2 barras vencidas', $movement->notes);

        // 2. Ajuste positivo (conteo mayor al stock: 12)
        $response2 = $this->post(route('inventory.adjust.store', $product), [
            'counted_stock' => 12,
            'reason' => 'Reconteo físico: 4 barras encontradas en congelador 2',
        ]);

        $response2->assertRedirect(route('inventory.kardex', ['product' => $product->id]));
        $product->refresh();
        $this->assertEquals(12, $product->stock);

        $movement2 = InventoryMovement::where('product_id', $product->id)
            ->where('concept', 'ajuste')
            ->where('type', 'entrada')
            ->first();

        $this->assertNotNull($movement2);
        $this->assertEquals(4, $movement2->quantity);
        $this->assertEquals(12, $movement2->balance);

        // 3. Validación: motivo con menos de 10 caracteres debe fallar
        $responseFail = $this->from(route('inventory.adjust', $product))->post(route('inventory.adjust.store', $product), [
            'counted_stock' => 15,
            'reason' => 'muy corto',
        ]);

        $responseFail->assertRedirect(route('inventory.adjust', $product));
        $responseFail->assertSessionHasErrors('reason');
    }

    public function test_security_audit_log_records_role_and_permission_changes(): void
    {
        $this->actingAs($this->admin);

        $targetUser = User::create([
            'name' => 'Operador Auditoría',
            'email' => 'operador@quickfood.internal',
            'password' => bcrypt('Password123!'),
            'active' => true,
        ]);

        $accessService = app(AccessControlService::class);

        // Asignar rol y permisos directos
        $accessService->saveUser($targetUser, [
            'name' => 'Operador Auditoría Modificado',
            'email' => 'operador@quickfood.internal',
            'active' => true,
            'roles' => ['almacenista'],
            'permissions' => ['ver-reportes'],
        ], $this->admin);

        $securityLog = Activity::where('log_name', 'seguridad')
            ->where('subject_type', $targetUser->getMorphClass())
            ->where('subject_id', $targetUser->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($securityLog, 'Debe registrarse en la bitácora de seguridad.');
        $this->assertEquals('roles y permisos del usuario modificados', $securityLog->description);

        // Modificar permisos de un rol
        $role = Role::findByName('vendedor');
        $accessService->syncRolePermissions($role, ['ver-productos', 'ver-clientes']);

        $roleLog = Activity::where('log_name', 'seguridad')
            ->where('subject_type', $role->getMorphClass())
            ->where('subject_id', $role->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($roleLog, 'Debe registrarse en la bitácora de seguridad el cambio de permisos de rol.');
        $this->assertStringContainsString('permisos del rol «vendedor» modificados', $roleLog->description);
    }

    public function test_kardex_and_audit_authorization_and_pdf_generation(): void
    {
        $product = Product::create([
            'nombre' => 'Gaseosa Postobón Manzana',
            'name' => 'Gaseosa Postobón Manzana',
            'precio' => 5000,
            'price' => 5000,
            'stock' => 20,
            'categoria_id' => $this->category->id,
            'category_id' => $this->category->id,
            'estado' => true,
            'active' => true,
        ]);

        app(InventoryService::class)->opening($product);

        // 1. Almacenista puede ver kardex pero NO auditoría
        $this->actingAs($this->almacenista);
        $this->get(route('inventory.kardex', ['product' => $product->id]))->assertOk();
        $this->get(route('audit.index'))->assertForbidden();

        // 2. Vendedor no puede ver ni kardex ni auditoría
        $this->actingAs($this->vendedor);
        $this->get(route('inventory.kardex', ['product' => $product->id]))->assertForbidden();
        $this->get(route('audit.index'))->assertForbidden();

        // 3. Admin tiene acceso a ambos y a generar el PDF
        $this->actingAs($this->admin);
        $this->get(route('audit.index'))->assertOk();

        $pdfResponse = $this->get(route('inventory.pdf', $product));
        $pdfResponse->assertOk();
        $this->assertEquals('application/pdf', $pdfResponse->headers->get('Content-Type'));
    }

    public function test_kardex_initialization_command(): void
    {
        $product = Product::create([
            'nombre' => 'Carne Molida Especial',
            'name' => 'Carne Molida Especial',
            'precio' => 18000,
            'price' => 18000,
            'stock' => 30,
            'categoria_id' => $this->category->id,
            'category_id' => $this->category->id,
            'estado' => true,
            'active' => true,
        ]);

        $this->assertFalse($product->movements()->exists());

        $this->artisan('kardex:inicializar')
            ->expectsOutputToContain('Productos inicializados en el kardex:')
            ->assertSuccessful();

        $this->assertTrue($product->fresh()->movements()->exists());
        $this->assertEquals(30, $product->fresh()->movements()->first()->balance);
    }
}
