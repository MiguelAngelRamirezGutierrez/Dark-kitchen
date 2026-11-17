@extends('layouts.app')

@section('title', 'Órdenes de Compra')
@section('page_title', 'Gestión de Compras & Reabastecimiento')

@php
    $statusColors = [
        'pendiente' => 'bg-amber-100 text-amber-800 border-amber-200',
        'parcial' => 'bg-blue-100 text-blue-800 border-blue-200',
        'recibida' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
        'anulada' => 'bg-slate-100 text-slate-700 border-slate-200',
    ];
@endphp

@section('content')
<div class="space-y-6">

    <!-- Barra Superior de Acciones y Métricas Resumen -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
        <div class="flex items-center space-x-3">
            @can('crear-compras')
                <a href="{{ route('purchases.create') }}" class="inline-flex items-center space-x-2 bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-cart-plus"></i>
                    <span>Nueva Orden de Compra</span>
                </a>
            @endcan

            <div class="hidden md:flex items-center space-x-4 pl-4 border-l border-slate-200 text-xs text-slate-600">
                <span><strong>Compras del mes:</strong> ${{ number_format((float) $totalMonth, 2) }}</span>
                <span><strong>Órdenes por recibir:</strong> <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full font-bold">{{ $pendingCount }}</span></span>
            </div>
        </div>

        <!-- Formulario de Filtros -->
        <form method="GET" action="{{ route('purchases.index') }}" class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            <div class="relative flex-1 sm:w-56">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Buscar por OC, factura o proveedor..."
                       class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 shadow-sm">
            </div>

            <select name="status" class="py-2 px-3 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-slate-700 shadow-sm">
                <option value="">Todos los estados</option>
                @foreach (['pendiente' => 'Pendiente', 'parcial' => 'Parcial', 'recibida' => 'Recibida', 'anulada' => 'Anulada'] as $value => $label)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                @endforeach
            </select>

            <input type="date" name="from" value="{{ request('from') }}" title="Desde fecha"
                   class="py-2 px-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-slate-700 shadow-sm">
            <input type="date" name="to" value="{{ request('to') }}" title="Hasta fecha"
                   class="py-2 px-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-slate-700 shadow-sm">

            <button type="submit" class="bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-3.5 py-2.5 rounded-xl shadow-sm transition">
                Filtrar
            </button>
            @if(request()->anyFilled(['search', 'status', 'from', 'to']))
                <a href="{{ route('purchases.index') }}" class="text-xs text-slate-500 hover:text-slate-800 font-medium px-2 py-2">
                    Limpiar
                </a>
            @endif
        </form>
    </div>

    <!-- Tabla de Órdenes de Compra -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 border-b border-slate-100 text-xs uppercase font-bold text-slate-500 tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4">Orden</th>
                        <th class="py-3.5 px-4">Fecha</th>
                        <th class="py-3.5 px-4">Proveedor</th>
                        <th class="py-3.5 px-4">Registró</th>
                        <th class="py-3.5 px-4 text-right">Total</th>
                        <th class="py-3.5 px-4 text-center">Estado</th>
                        <th class="py-3.5 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($purchases as $purchase)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $purchase->purchase_number }}
                                @if($purchase->provider_invoice)
                                    <span class="block text-xs font-normal text-slate-400">Fact: {{ $purchase->provider_invoice }}</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-xs font-medium text-slate-700">
                                {{ $purchase->purchase_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-900">
                                {{ $purchase->provider?->name ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-500">
                                {{ $purchase->user?->name ?? '—' }}
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900">
                                ${{ number_format((float) $purchase->total, 2) }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $statusColors[$purchase->status] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ ucfirst($purchase->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('purchases.show', $purchase) }}" class="text-orange-600 hover:text-orange-800 text-xs font-semibold hover:underline">
                                    <i class="fa-solid fa-eye mr-1"></i>Ver
                                </a>
                                <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank" class="text-slate-600 hover:text-slate-800 text-xs font-semibold hover:underline">
                                    <i class="fa-solid fa-file-pdf text-red-500 mr-1"></i>PDF
                                </a>
                                @can('eliminar-compras')
                                    @if ($purchase->canBeCancelled())
                                        <form method="POST" action="{{ route('purchases.destroy', $purchase) }}" class="inline"
                                              onsubmit="return confirm('¿Anular esta orden de compra? Esta acción no se puede deshacer.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 text-xs font-semibold hover:underline">
                                                <i class="fa-solid fa-ban mr-1"></i>Anular
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-boxes-packing text-3xl mb-2 text-slate-300"></i>
                                <p>No se encontraron órdenes de compra registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($purchases->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $purchases->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
