<?php

namespace Tests\Feature;

use App\Events\SaleRegistered;
use App\Models\Client;
use App\Models\Product;
use App\Models\Provider;
use App\Models\Purchase;
use App\Models\User;
use App\Notifications\DailySummaryNotification;
use App\Notifications\LowStockNotification;
use App\Notifications\PurchaseReceivedNotification;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

/**
 * Clase 14: eventos, listeners, notificaciones y tareas programadas.
 */
class Clase14Test extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->seed(DatabaseSeeder::class);
    }

    private function user(string $email): User
    {
        return User::where('email', $email)->firstOrFail();
    }

    private function sell(string $product, int $qty)
    {
        return $this->actingAs($this->user('admin@test.com'))->post('/sales', [
            'client_id' => Client::first()->id, 'sale_date' => now()->toDateString(),
            'items' => [['product_id' => Product::where('name', $product)->value('id'), 'quantity' => $qty]],
        ]);
    }

    public function test_la_venta_dispara_el_evento(): void
    {
        Event::fake([SaleRegistered::class]);
        $this->sell('Mouse Logitech', 1)->assertRedirect();
        Event::assertDispatched(SaleRegistered::class, fn ($e) => str_starts_with($e->sale->invoice_number, 'FV-'));

        // Venta fallida (sin stock): no hay evento
        Event::fake([SaleRegistered::class]);
        $this->sell('Mouse Logitech', 999)->assertSessionHasErrors('items');
        Event::assertNotDispatched(SaleRegistered::class);
    }

    public function test_alerta_de_stock_bajo_solo_al_cruzar_el_umbral(): void
    {
        Notification::fake();

        // Diadema: 8 → 6 (no cruza)
        $this->sell('Diadema HyperX', 2);
        Notification::assertNothingSent();

        // 6 → 5 (cruza el umbral de 5) → alerta a quienes pueden crear compras
        $this->sell('Diadema HyperX', 1);
        Notification::assertSentTo($this->user('almacenista@test.com'), LowStockNotification::class,
            fn ($n, $channels) => $n->product->name === 'Diadema HyperX' && $channels === ['mail', 'database']);
        Notification::assertSentTo($this->user('admin@test.com'), LowStockNotification::class);
        Notification::assertSentTo($this->user('supervisor@test.com'), LowStockNotification::class); // rol almacenista
        Notification::assertNotSentTo($this->user('vendedor@test.com'), LowStockNotification::class);

        // 5 → 4 (ya estaba bajo): no repite
        Notification::fake();
        $this->sell('Diadema HyperX', 1);
        Notification::assertNothingSent();
    }

    public function test_recepcion_de_compra_notifica_con_pdf_adjunto(): void
    {
        Notification::fake();
        $alm = $this->user('almacenista@test.com');
        $this->actingAs($alm)->post('/purchases', [
            'provider_id' => Provider::first()->id, 'purchase_date' => now()->toDateString(),
            'items' => [['product_id' => Product::first()->id, 'quantity' => 3, 'unit_cost' => 1000]],
        ]);
        $purchase = Purchase::latest('id')->first();
        $this->actingAs($alm)->patch("/purchases/{$purchase->id}/receive", ['items' => [$purchase->details->first()->id => 3]]);

        Notification::assertSentTo($alm, PurchaseReceivedNotification::class, function ($n) use ($alm) {
            $mail = $n->toMail($alm);

            return $n->complete && count($mail->rawAttachments) === 1
                && str_starts_with($mail->rawAttachments[0]['name'], 'OC-');
        });
    }

    public function test_campana_de_notificaciones(): void
    {
        $alm = $this->user('almacenista@test.com');
        $alm->notify(new LowStockNotification(Product::first())); // canal database real (cola sync en pruebas)

        $this->actingAs($alm)->get('/dashboard')->assertOk()->assertSee('title="Notificaciones"', false);
        $this->actingAs($alm)->get('/notificaciones')->assertOk()->assertSee('Stock bajo');

        $id = $alm->notifications()->first()->id;
        $this->actingAs($alm)->patch("/notificaciones/{$id}")->assertRedirect();
        $this->assertSame(0, $alm->fresh()->unreadNotifications()->count());

        // No puede abrir la notificación de otro usuario
        $other = $this->user('vendedor@test.com');
        $other->notify(new LowStockNotification(Product::first()));
        $this->actingAs($alm)->patch('/notificaciones/'.$other->notifications()->first()->id)->assertNotFound();

        $this->actingAs($other)->post('/notificaciones/leer-todas')->assertSessionHas('success');
        $this->assertSame(0, $other->fresh()->unreadNotifications()->count());
    }

    public function test_resumen_diario_y_programacion(): void
    {
        Notification::fake();
        $this->sell('Mouse Logitech', 2);

        $this->artisan('erp:resumen-diario')->expectsOutputToContain('usuario(s)')->assertSuccessful();
        Notification::assertSentTo($this->user('admin@test.com'), DailySummaryNotification::class,
            fn ($n) => $n->summary['sales_count'] >= 1 && in_array('Monitor Samsung', $n->summary['low_stock']));
        Notification::assertSentTo($this->user('supervisor@test.com'), DailySummaryNotification::class); // permiso directo ver-reportes

        Artisan::call('schedule:list');
        $output = Artisan::output();
        $this->assertStringContainsString('erp:resumen-diario', $output);
        $this->assertStringContainsString('activitylog:clean', $output);
    }
}
