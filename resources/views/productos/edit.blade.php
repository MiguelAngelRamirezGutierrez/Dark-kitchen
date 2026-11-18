@extends('layouts.app')

@section('title', 'Editar Producto')
@section('page_title', 'Editar Producto #' . $producto->id)

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-slate-800">Modificar Producto</h2>
            <p class="text-xs text-slate-500">Actualice precios, ingredientes o disponibilidad del producto.</p>
        </div>
        <a href="{{ route('productos.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 bg-white border border-slate-200 px-3 py-1.5 rounded-lg shadow-sm transition">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Productos
        </a>
    </div>

    <form action="{{ route('productos.update', $producto) }}" method="POST" class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        @csrf
        @method('PUT')

        <div class="space-y-4 text-xs">
            <div>
                <label for="categoria_id" class="block font-semibold text-slate-700 mb-1">Categoría <span class="text-rose-500">*</span></label>
                <select name="categoria_id" id="categoria_id" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ old('categoria_id', $producto->categoria_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="nombre" class="block font-semibold text-slate-700 mb-1">Nombre del Producto <span class="text-rose-500">*</span></label>
                <input type="text" name="nombre" id="nombre" value="{{ old('nombre', $producto->nombre) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
            </div>

            <div>
                <label for="descripcion" class="block font-semibold text-slate-700 mb-1">Descripción de Ingredientes</label>
                <textarea name="descripcion" id="descripcion" rows="3" class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">{{ old('descripcion', $producto->descripcion) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="precio" class="block font-semibold text-slate-700 mb-1">Precio de Venta (COP) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" min="0" name="precio" id="precio" value="{{ old('precio', $producto->precio) }}" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Stock Actual (Solo Lectura)</label>
                    <div class="flex items-center space-x-3 p-2 bg-slate-50 border border-slate-200 rounded-lg">
                        <span class="text-base font-bold text-slate-900">{{ $producto->stock }} unidades</span>
                    </div>
                    <div class="mt-1 space-x-3 text-xs">
                        @can('ver-inventario')
                            <a href="{{ route('inventory.kardex', ['product' => $producto->id]) }}" class="text-blue-600 hover:text-blue-800 underline font-semibold">
                                <i class="fa-solid fa-boxes-stacked mr-1"></i>Ver kardex
                            </a>
                        @endcan
                        @can('ajustar-inventario')
                            <a href="{{ route('inventory.adjust', $producto) }}" class="text-orange-600 hover:text-orange-800 underline font-semibold">
                                <i class="fa-solid fa-sliders mr-1"></i>Ajustar inventario
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div>
                <label for="estado" class="block font-semibold text-slate-700 mb-1">Estado de Disponibilidad <span class="text-rose-500">*</span></label>
                <select name="estado" id="estado" required class="w-full rounded-lg border border-slate-300 p-2.5 focus:ring-2 focus:ring-orange-500 focus:outline-none">
                    <option value="1" {{ old('estado', $producto->estado ? '1' : '0') == '1' ? 'selected' : '' }}>Activo</option>
                    <option value="0" {{ old('estado', $producto->estado ? '1' : '0') == '0' ? 'selected' : '' }}>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
            <a href="{{ route('productos.index') }}" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-xs font-semibold hover:bg-slate-50 transition">
                Cancelar
            </a>
            <button type="submit" class="px-5 py-2 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-md transition">
                Actualizar Producto
            </button>
        </div>
    </form>

</div>
@endsection
