<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DomiciliarioController;
use App\Http\Controllers\MetodoPagoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - QuickFood ERP (Clase 7: Roles, Permisos y CRUD Completo)
|--------------------------------------------------------------------------
*/

// Rutas de Autenticación y Conmutador Rápido de Roles (Paso 11 de la Guía)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/quick-login/{user}', [AuthController::class, 'quickLogin'])->name('quick-login');

// Redirección principal: si no está logueado va a login, si está logueado va a dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Panel de control / Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// --- MÓDULO PRODUCTOS (Con Soft Deletes y Permisos) ---
Route::patch('productos/{producto}/restore', [ProductoController::class, 'restore'])
    ->withTrashed()
    ->name('productos.restore');
Route::resource('productos', ProductoController::class);

// Alias en inglés para compatibilidad con la guía (Paso 8)
Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
    ->withTrashed()
    ->name('products.restore');
Route::resource('products', ProductController::class)->except('show');

// --- MÓDULO CATEGORÍAS (Con Soft Deletes y Permisos) ---
Route::patch('categorias/{categoria}/restore', [CategoriaController::class, 'restore'])
    ->withTrashed()
    ->name('categorias.restore');
Route::resource('categorias', CategoriaController::class);

// Alias en inglés para categorías (Paso 8)
Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
    ->withTrashed()
    ->name('categories.restore');
Route::resource('categories', CategoryController::class)->except('show');

// --- OTROS MÓDULOS DEL ERP ---
Route::resource('pedidos', PedidoController::class);
Route::patch('pedidos/{pedido}/cambiar-estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
Route::resource('clientes', ClienteController::class);
Route::resource('domiciliarios', DomiciliarioController::class);
Route::resource('metodos-pago', MetodoPagoController::class);
