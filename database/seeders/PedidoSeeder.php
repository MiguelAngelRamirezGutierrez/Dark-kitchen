<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\Domiciliario;
use App\Models\MetodoPago;
use App\Models\Pedido;
use App\Models\PedidoDetalle;
use App\Models\Producto;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PedidoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Pedido::exists()) {
            return;
        }

        // --- Lookup maps (name -> id) so IDs are not hardcoded ---
        $clientes      = Cliente::pluck('id', 'nombre');
        $domiciliarios = Domiciliario::pluck('id', 'nombre');
        $metodosPago   = MetodoPago::pluck('id', 'nombre');
        $productos     = Producto::pluck('id', 'nombre');

        $c  = fn(string $n) => $clientes->firstWhere(fn($v, $k) => str_starts_with($k, explode(' ', $n)[0]))
            ?? $clientes->first();
        // Simpler: map by exact first name keyword
        $cl = fn(int $i) => $clientes->values()->get($i - 1);    // 1-based index
        $do = fn(?int $i) => $i ? $domiciliarios->values()->get($i - 1) : null;  // 1-based, nullable
        $mp = fn(int $i) => $metodosPago->values()->get($i - 1); // 1-based index
        $pr = fn(int $i) => $productos->values()->get($i - 1);    // 1-based index

        $pedidosData = [
            // 1. Recibido
            [
                'cliente_id'      => $cl(1),
                'domiciliario_id' => null,
                'metodo_pago_id'  => $mp(2), // Nequi
                'direccion_entrega' => 'Carrera 15 # 45-20 Apto 302, Barrio El Bosque',
                'estado'          => 'Recibido',
                'observaciones'   => 'Timbrar en portería y anunciar que es de QuickFood.',
                'created_at'      => Carbon::now()->subMinutes(10),
                'items'           => [
                    ['producto_id' => $pr(1),  'cantidad' => 2],
                    ['producto_id' => $pr(16), 'cantidad' => 2],
                ],
            ],
            // 2. Preparando
            [
                'cliente_id'      => $cl(2),
                'domiciliario_id' => null,
                'metodo_pago_id'  => $mp(1), // Efectivo
                'direccion_entrega' => 'Calle 28 # 12-40, Barrio San Fernando',
                'estado'          => 'Preparando',
                'observaciones'   => 'Sin cebolla en la hamburguesa y salsas aparte.',
                'created_at'      => Carbon::now()->subMinutes(25),
                'items'           => [
                    ['producto_id' => $pr(3),  'cantidad' => 1],
                    ['producto_id' => $pr(11), 'cantidad' => 1],
                    ['producto_id' => $pr(14), 'cantidad' => 1],
                ],
            ],
            // 3. Preparando - Combo
            [
                'cliente_id'      => $cl(3),
                'domiciliario_id' => null,
                'metodo_pago_id'  => $mp(4), // Bancolombia
                'direccion_entrega' => 'Avenida Las Palmas # 10-55 Casa 4, Conjunto Los Álamos',
                'estado'          => 'Preparando',
                'observaciones'   => 'Casa esquinera blanca con portón café.',
                'created_at'      => Carbon::now()->subMinutes(35),
                'items'           => [
                    ['producto_id' => $pr(20), 'cantidad' => 1],
                ],
            ],
            // 4. Listo
            [
                'cliente_id'      => $cl(4),
                'domiciliario_id' => $do(1), // Diego Rojas
                'metodo_pago_id'  => $mp(3), // Daviplata
                'direccion_entrega' => 'Manzana B Casa 12, Urbanización Los Sauces',
                'estado'          => 'Listo',
                'observaciones'   => 'Pedido empacado en bolsa térmica biodegradable.',
                'created_at'      => Carbon::now()->subMinutes(45),
                'items'           => [
                    ['producto_id' => $pr(5),  'cantidad' => 2],
                    ['producto_id' => $pr(12), 'cantidad' => 1],
                    ['producto_id' => $pr(16), 'cantidad' => 2],
                ],
            ],
            // 5. Listo
            [
                'cliente_id'      => $cl(5),
                'domiciliario_id' => $do(2), // Jhonatan Castro
                'metodo_pago_id'  => $mp(2), // Nequi
                'direccion_entrega' => 'Transversal 5 # 18-90, Barrio El Jardín',
                'estado'          => 'Listo',
                'observaciones'   => 'Cliente pagó por Nequi anticipadamente. Comprobante verificado.',
                'created_at'      => Carbon::now()->subMinutes(50),
                'items'           => [
                    ['producto_id' => $pr(9),  'cantidad' => 1],
                    ['producto_id' => $pr(15), 'cantidad' => 1],
                ],
            ],
            // 6. En camino
            [
                'cliente_id'      => $cl(6),
                'domiciliario_id' => $do(3), // Cristian Pineda
                'metodo_pago_id'  => $mp(1), // Efectivo
                'direccion_entrega' => 'Calle 40 # 25-14 Interior 2, Barrio Santa Clara',
                'estado'          => 'En camino',
                'observaciones'   => 'Llevar cambio de $50.000.',
                'created_at'      => Carbon::now()->subHours(1),
                'items'           => [
                    ['producto_id' => $pr(4),  'cantidad' => 1],
                    ['producto_id' => $pr(13), 'cantidad' => 1],
                ],
            ],
            // 7. En camino
            [
                'cliente_id'      => $cl(7),
                'domiciliario_id' => $do(4), // Brayan Ortiz
                'metodo_pago_id'  => $mp(5), // Tarjeta Débito
                'direccion_entrega' => 'Carrera 8 # 22-16 Bloque 3 Apto 204',
                'estado'          => 'En camino',
                'observaciones'   => 'Número de portero: ext. 103.',
                'created_at'      => Carbon::now()->subHours(1)->subMinutes(15),
                'items'           => [
                    ['producto_id' => $pr(7),  'cantidad' => 2],
                    ['producto_id' => $pr(17), 'cantidad' => 2],
                ],
            ],
            // 8. Entregado
            [
                'cliente_id'      => $cl(8),
                'domiciliario_id' => $do(5), // Yeison Guerrero
                'metodo_pago_id'  => $mp(1), // Efectivo
                'direccion_entrega' => 'Diagonal 14 # 33-08, Barrio La Pradera',
                'estado'          => 'Entregado',
                'observaciones'   => 'Entregado a satisfacción del cliente.',
                'created_at'      => Carbon::now()->subHours(2),
                'items'           => [
                    ['producto_id' => $pr(2),  'cantidad' => 1],
                    ['producto_id' => $pr(18), 'cantidad' => 1],
                ],
            ],
            // 9. Entregado
            [
                'cliente_id'      => $cl(9),
                'domiciliario_id' => $do(6), // Edwin Moreno
                'metodo_pago_id'  => $mp(2), // Nequi
                'direccion_entrega' => 'Carrera 19 # 50-12 Piso 2, Barrio Bolívar',
                'estado'          => 'Entregado',
                'observaciones'   => 'Entrega sin novedades.',
                'created_at'      => Carbon::now()->subHours(3),
                'items'           => [
                    ['producto_id' => $pr(10), 'cantidad' => 1],
                    ['producto_id' => $pr(19), 'cantidad' => 1],
                ],
            ],
            // 10. Entregado
            [
                'cliente_id'      => $cl(10),
                'domiciliario_id' => $do(7), // Kevin Guzmán
                'metodo_pago_id'  => $mp(4), // Bancolombia
                'direccion_entrega' => 'Calle 11 # 16-25, Barrio San Jorge',
                'estado'          => 'Entregado',
                'observaciones'   => 'Cliente frecuente de la zona.',
                'created_at'      => Carbon::now()->subDay()->subHours(4),
                'items'           => [
                    ['producto_id' => $pr(8),  'cantidad' => 1],
                    ['producto_id' => $pr(16), 'cantidad' => 3],
                ],
            ],
            // 11. Entregado
            [
                'cliente_id'      => $cl(1),
                'domiciliario_id' => $do(8), // Sebastián Ramos
                'metodo_pago_id'  => $mp(6), // Tarjeta Crédito
                'direccion_entrega' => 'Carrera 15 # 45-20 Apto 302, Barrio El Bosque',
                'estado'          => 'Entregado',
                'observaciones'   => 'Pago aprobado exitosamente mediante datáfono.',
                'created_at'      => Carbon::now()->subDays(2),
                'items'           => [
                    ['producto_id' => $pr(6),  'cantidad' => 2],
                    ['producto_id' => $pr(11), 'cantidad' => 1],
                ],
            ],
            // 12. Cancelado
            [
                'cliente_id'      => $cl(2),
                'domiciliario_id' => null,
                'metodo_pago_id'  => $mp(1), // Efectivo
                'direccion_entrega' => 'Calle 28 # 12-40, Barrio San Fernando',
                'estado'          => 'Cancelado',
                'observaciones'   => 'Cancelado por solicitud del cliente antes de ingresar a preparación.',
                'created_at'      => Carbon::now()->subDays(3),
                'items'           => [
                    ['producto_id' => $pr(1), 'cantidad' => 1],
                ],
            ],
        ];

        foreach ($pedidosData as $data) {
            $items = $data['items'];
            unset($data['items']);

            // Skip if any FK is null (lookup failed)
            if (!$data['cliente_id'] || !$data['metodo_pago_id']) {
                continue;
            }

            $pedido = Pedido::create($data);

            $totalCalculado = 0;

            foreach ($items as $item) {
                if (!$item['producto_id']) {
                    continue;
                }

                $producto = Producto::find($item['producto_id']);
                if (!$producto) {
                    continue;
                }

                $subtotal = $producto->precio * $item['cantidad'];

                PedidoDetalle::create([
                    'pedido_id'       => $pedido->id,
                    'producto_id'     => $producto->id,
                    'cantidad'        => $item['cantidad'],
                    'precio_unitario' => $producto->precio,
                    'subtotal'        => $subtotal,
                ]);

                $totalCalculado += $subtotal;

                if ($pedido->estado !== 'Cancelado') {
                    $producto->decrement('stock', $item['cantidad']);
                }
            }

            $pedido->update(['total' => $totalCalculado]);
        }
    }
}
