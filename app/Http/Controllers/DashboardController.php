<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Cliente;
use App\Models\Domiciliario;
use App\Models\Pedido;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Muestra el panel principal del ERP con métricas operativas y módulos según roles y permisos.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $roles = $user ? $user->getRoleNames() : collect();

        // Módulos dinámicos según permisos del usuario (Paso 10 de la Guía)
        $modules = collect([
            [
                'title' => 'Productos',
                'description' => ($user && $user->can('crear-productos'))
                    ? 'Consulta y administra el inventario de platos y alimentos.'
                    : 'Consulta el catálogo y precios de productos.',
                'route' => 'productos.index',
                'permission' => 'ver-productos',
                'icon' => 'fa-burger',
            ],
            [
                'title' => 'Categorías',
                'description' => ($user && $user->can('crear-categorias'))
                    ? 'Organiza los productos por categorías gastronómicas.'
                    : 'Consulta las categorías del menú.',
                'route' => 'categorias.index',
                'permission' => 'ver-categorias',
                'icon' => 'fa-tags',
            ],
            [
                'title' => 'Pedidos',
                'description' => ($user && $user->can('crear-pedidos'))
                    ? 'Gestiona órdenes de cocina, comandas y despachos.'
                    : 'Consulta los pedidos en cocina.',
                'route' => 'pedidos.index',
                'permission' => 'ver-pedidos',
                'icon' => 'fa-clipboard-list',
            ],
            [
                'title' => 'Clientes',
                'description' => ($user && $user->can('crear-clientes'))
                    ? 'Administra el directorio de clientes y direcciones.'
                    : 'Directorio de clientes para entrega.',
                'route' => 'clientes.index',
                'permission' => 'ver-clientes',
                'icon' => 'fa-users',
            ],
            [
                'title' => 'Domiciliarios',
                'description' => 'Supervisa la flota de reparto y vehículos.',
                'route' => 'domiciliarios.index',
                'permission' => 'ver-domiciliarios',
                'icon' => 'fa-motorcycle',
            ],
        ])->filter(function (array $module) use ($user) {
            return !$user || $user->hasRole('admin') || $user->can($module['permission']);
        })->values();

        // Estadísticas según permisos
        $stats = collect();
        if (!$user || $user->can('ver-productos') || $user->hasRole('admin')) {
            $stats->push(['label' => 'Productos activos', 'value' => Producto::where('estado', true)->count()]);
            $stats->push(['label' => 'Stock bajo (≤ 5)', 'value' => Producto::where('estado', true)->where('stock', '<=', 5)->count()]);
            $stats->push(['label' => 'Valor del inventario', 'value' => '$ ' . number_format((float) Producto::where('estado', true)->sum(DB::raw('precio * stock')), 0, ',', '.')]);
        }
        if (!$user || $user->can('ver-categorias') || $user->hasRole('admin')) {
            $stats->push(['label' => 'Categorías activas', 'value' => Categoria::where('estado', true)->count()]);
        }

        // Métricas operativas del ERP
        $pedidosHoy = Pedido::hoy()->count();
        $pedidosPendientes = Pedido::pendientes()->count();
        $pedidosEntregados = Pedido::entregados()->count();
        $productosActivos = Producto::activos()->count();
        $ventasHoy = Pedido::hoy()->where('estado', '!=', 'Cancelado')->sum('total');

        $pedidosRecientes = Pedido::with(['cliente', 'domiciliario', 'metodoPago'])
            ->latest()
            ->take(8)
            ->get();

        $productosBajoStock = Producto::with('categoria')
            ->where('stock', '<=', 25)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        $estadosConteo = [
            'Recibido' => Pedido::where('estado', 'Recibido')->count(),
            'Preparando' => Pedido::where('estado', 'Preparando')->count(),
            'Listo' => Pedido::where('estado', 'Listo')->count(),
            'En camino' => Pedido::where('estado', 'En camino')->count(),
            'Entregado' => Pedido::where('estado', 'Entregado')->count(),
            'Cancelado' => Pedido::where('estado', 'Cancelado')->count(),
        ];

        return view('dashboard', compact(
            'user',
            'roles',
            'modules',
            'stats',
            'pedidosHoy',
            'pedidosPendientes',
            'pedidosEntregados',
            'productosActivos',
            'ventasHoy',
            'pedidosRecientes',
            'productosBajoStock',
            'estadosConteo'
        ));
    }

    /**
     * Soporte para invocación directa como Action invokable.
     */
    public function __invoke(Request $request)
    {
        return $this->index($request);
    }
}
