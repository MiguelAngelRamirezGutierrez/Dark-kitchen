@extends('layouts.app')

@section('title', 'Orden de Compra ' . $purchase->purchase_number)
@section('page_title', 'Detalle de Orden de Compra & Recepción')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Acciones Superiores -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 no-print">
        <a href="{{ route('purchases.index') }}" class="inline-flex items-center space-x-1.5 text-sm font-semibold text-slate-600 hover:text-slate-800 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Volver a Compras</span>
        </a>

        <div class="flex items-center space-x-2">
            <!-- Descargar PDF -->
            <a href="{{ route('purchases.pdf', $purchase) }}" target="_blank"
               class="inline-flex items-center space-x-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-file-pdf"></i>
                <span>Descargar PDF</span>
            </a>

            <!-- Imprimir -->
            <button onclick="window.print()" class="inline-flex items-center space-x-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition">
                <i class="fa-solid fa-print"></i>
                <span>Imprimir</span>
            </button>

            <!-- Anular si aplica -->
            @can('eliminar-compras')
                @if($purchase->canBeCancelled())
                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST"
                          onsubmit="return confirm('¿Está seguro de anular la orden {{ $purchase->purchase_number }}? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center space-x-1.5 bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 text-xs font-semibold px-4 py-2.5 rounded-xl transition">
                            <i class="fa-solid fa-ban"></i>
                            <span>Anular Orden</span>
                        </button>
                    </form>
                @endif
            @endcan
        </div>
    </div>

    <!-- Tarjeta Principal de la Orden -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 space-y-8">

        <!-- Encabezado de la Orden -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-tr from-amber-600 to-orange-500 flex items-center justify-center text-white shadow-md">
                        <i class="fa-solid fa-truck-ramp-box"></i>
                    </div>
                    <span class="text-xl font-black text-slate-900 tracking-tight">QuickFood ERP</span>
                </div>
                <p class="text-xs text-slate-500">Módulo de Compras & Reabastecimiento de Insumos</p>
                <p class="text-xs text-slate-400">Dark Kitchen · NIT: 900.123.456-7</p>
            </div>

            <div class="sm:text-right">
                <div class="flex items-center sm:justify-end space-x-2">
                    <span class="inline-block bg-slate-100 border border-slate-200 text-slate-800 font-mono font-bold text-sm px-3 py-1 rounded-lg">
                        ORDEN: {{ $purchase->purchase_number }}
                    </span>
                    @php
                        $statusBadge = match($purchase->status) {
                            'recibida' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'parcial' => 'bg-amber-100 text-amber-800 border-amber-200',
                            'anulada' => 'bg-red-100 text-red-800 border-red-200',
                            default => 'bg-blue-100 text-blue-800 border-blue-200',
                        };
                    @endphp
                    <span class="text-xs px-2.5 py-1 rounded-full font-bold uppercase border {{ $statusBadge }}">
                        {{ $purchase->status }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 mt-2">
                    <strong>Fecha Emisión:</strong> {{ $purchase->purchase_date ? $purchase->purchase_date->format('d/m/Y') : '' }}
                </p>
                <p class="text-xs text-slate-500">
                    <strong>Registrado por:</strong> {{ $purchase->user?->name ?? 'Sistema' }}
                </p>
                @if($purchase->provider_invoice)
                    <p class="text-xs font-semibold text-slate-700">
                        <strong>Factura Proveedor:</strong> {{ $purchase->provider_invoice }}
                    </p>
                @endif
            </div>
        </div>

        <!-- Información del Proveedor -->
        <div class="bg-slate-50/75 p-5 rounded-xl border border-slate-100">
            <p class="text-xs uppercase font-bold text-slate-400 tracking-wider mb-2">Datos del Proveedor</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="font-bold text-slate-800">{{ $purchase->provider?->name ?? 'Proveedor no especificado' }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        <span class="font-semibold">{{ $purchase->provider?->document_type }}</span>: {{ $purchase->provider?->document_number }}
                    </p>
                    @if($purchase->provider?->contact_name)
                        <p class="text-xs text-slate-600 mt-1">
                            <span class="font-medium text-slate-400">Contacto:</span> {{ $purchase->provider->contact_name }}
                        </p>
                    @endif
                </div>
                <div class="text-xs text-slate-600 space-y-1">
                    @if($purchase->provider?->email)
                        <p><i class="fa-regular fa-envelope text-slate-400 mr-1.5"></i>{{ $purchase->provider->email }}</p>
                    @endif
                    @if($purchase->provider?->phone)
                        <p><i class="fa-solid fa-phone text-slate-400 mr-1.5"></i>{{ $purchase->provider->phone }}</p>
                    @endif
                    @if($purchase->provider?->address)
                        <p><i class="fa-solid fa-location-dot text-slate-400 mr-1.5"></i>{{ $purchase->provider->address }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tabla de Items de la Orden -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm divide-y divide-slate-100">
                <thead>
                    <tr class="text-xs uppercase text-slate-400 font-semibold text-left">
                        <th class="py-3">Producto / Insumo</th>
                        <th class="py-3 text-center">Pedido</th>
                        <th class="py-3 text-center">Recibido</th>
                        <th class="py-3 text-center">Pendiente</th>
                        <th class="py-3 text-right">Costo Unit.</th>
                        <th class="py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @foreach ($purchase->details as $detail)
                        <tr>
                            <td class="py-3">
                                <span class="font-medium text-slate-900 block">
                                    {{ $detail->product?->name ?? 'Producto #' . $detail->product_id }}
                                </span>
                                <span class="text-xs text-slate-400">Stock actual en bodega: {{ $detail->product?->stock ?? 0 }}</span>
                            </td>
                            <td class="py-3 text-center font-mono font-semibold text-slate-900">
                                {{ $detail->quantity }}
                            </td>
                            <td class="py-3 text-center font-mono font-semibold {{ $detail->quantity_received > 0 ? 'text-emerald-600' : 'text-slate-400' }}">
                                {{ $detail->quantity_received }}
                            </td>
                            <td class="py-3 text-center font-mono font-bold {{ $detail->pendingQuantity() > 0 ? 'text-amber-600' : 'text-slate-300' }}">
                                {{ $detail->pendingQuantity() }}
                            </td>
                            <td class="py-3 text-right font-mono text-slate-600">
                                ${{ number_format((float) $detail->unit_cost, 2, ',', '.') }}
                            </td>
                            <td class="py-3 text-right font-mono font-bold text-slate-900">
                                ${{ number_format((float) $detail->subtotal, 2, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totales y Notas -->
        <div class="flex flex-col sm:flex-row justify-between items-start gap-4 pt-4 border-t border-slate-100">
            <div class="text-xs text-slate-500 max-w-sm space-y-2">
                @if($purchase->notes)
                    <p class="font-bold text-slate-700">Notas de la Orden:</p>
                    <p class="italic bg-slate-50 p-2.5 rounded-lg border border-slate-100">{{ $purchase->notes }}</p>
                @endif
                <p class="text-[11px] text-slate-400">
                    <i class="fa-solid fa-circle-info mr-1"></i>
                    El stock solo se incrementa al registrar la recepción de los productos en bodega.
                </p>
            </div>

            <div class="w-full sm:w-64 space-y-2 text-sm">
                <div class="flex justify-between text-slate-600 text-xs">
                    <span>Subtotal Neto:</span>
                    <span class="font-mono font-semibold">${{ number_format((float) $purchase->subtotal, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-600 text-xs">
                    <span>IVA (19%):</span>
                    <span class="font-mono font-semibold">${{ number_format((float) $purchase->tax, 2, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-base font-bold text-slate-900 pt-2 border-t border-slate-100">
                    <span>Total Orden:</span>
                    <span class="font-mono text-amber-600">${{ number_format((float) $purchase->total, 2, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- SECCIÓN INTERACTIVA: Formulario de Recepción de Mercancía -->
    @can('recibir-compras')
        @if($purchase->canBeReceived())
            <div class="bg-gradient-to-br from-amber-50 to-orange-50 rounded-2xl shadow-sm border border-amber-200 p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-lg shadow-sm">
                            <i class="fa-solid fa-box-open"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">Recepción de Mercancía en Bodega</h3>
                            <p class="text-xs text-slate-600">Ingrese la cantidad que efectivamente llegó en esta entrega para actualizar el inventario.</p>
                        </div>
                    </div>
                    <button type="button" id="fillAllBtn" class="text-xs font-semibold text-amber-800 hover:text-amber-950 underline cursor-pointer">
                        Recibir todo el faltante
                    </button>
                </div>

                <form action="{{ route('purchases.receive', $purchase) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="max-w-xs">
                        <label for="provider_invoice" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Factura / Remisión del Proveedor (Opcional)
                        </label>
                        <input type="text" name="provider_invoice" id="provider_invoice"
                               value="{{ old('provider_invoice', $purchase->provider_invoice) }}"
                               placeholder="Ej: FE-98765"
                               class="w-full px-3 py-2 text-sm rounded-xl border border-amber-300 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @error('provider_invoice')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-white rounded-xl border border-amber-200 overflow-hidden">
                        <table class="min-w-full text-sm divide-y divide-slate-100">
                            <thead class="bg-amber-100/60 text-slate-700 text-xs uppercase font-semibold text-left">
                                <tr>
                                    <th class="py-3 px-4">Producto</th>
                                    <th class="py-3 px-4 text-center">Pedido</th>
                                    <th class="py-3 px-4 text-center">Ya recibido</th>
                                    <th class="py-3 px-4 text-center">Pendiente</th>
                                    <th class="py-3 px-4 text-right w-44">Llega ahora</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($purchase->details as $detail)
                                    @php $pending = $detail->pendingQuantity(); @endphp
                                    <tr class="hover:bg-amber-50/50">
                                        <td class="py-3 px-4 font-medium text-slate-800">
                                            {{ $detail->product?->name }}
                                        </td>
                                        <td class="py-3 px-4 text-center font-mono">{{ $detail->quantity }}</td>
                                        <td class="py-3 px-4 text-center font-mono text-emerald-600">{{ $detail->quantity_received }}</td>
                                        <td class="py-3 px-4 text-center font-mono font-bold text-amber-700">{{ $pending }}</td>
                                        <td class="py-3 px-4 text-right">
                                            @if($pending > 0)
                                                <input type="number"
                                                       name="items[{{ $detail->id }}]"
                                                       min="0"
                                                       max="{{ $pending }}"
                                                       value="{{ old("items.{$detail->id}", 0) }}"
                                                       data-pending="{{ $pending }}"
                                                       class="receive-input w-24 text-right font-mono font-bold px-3 py-1.5 text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                            @else
                                                <span class="text-xs text-emerald-600 font-semibold">Completado</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @error('items')
                        <p class="text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-end space-x-3">
                        <button type="submit"
                                class="inline-flex items-center space-x-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white font-semibold text-sm px-6 py-2.5 rounded-xl shadow-md transition">
                            <i class="fa-solid fa-dolly"></i>
                            <span>Confirmar Entrada a Inventario</span>
                        </button>
                    </div>
                </form>
            </div>

            <script>
                document.getElementById('fillAllBtn')?.addEventListener('click', function() {
                    document.querySelectorAll('.receive-input').forEach(function(input) {
                        input.value = input.getAttribute('data-pending') || 0;
                    });
                });
            </script>
        @else
            <div class="bg-slate-50 border border-slate-200 rounded-2xl p-6 text-center text-sm text-slate-600">
                @if($purchase->status === 'recibida')
                    <i class="fa-solid fa-circle-check text-emerald-500 text-2xl mb-2 block"></i>
                    <p class="font-bold text-slate-800">Orden Recibida en su Totalidad</p>
                    <p class="text-xs text-slate-500 mt-1">Todos los productos pedidos han ingresado exitosamente a la bodega e incrementado el stock.</p>
                @elseif($purchase->status === 'anulada')
                    <i class="fa-solid fa-ban text-red-500 text-2xl mb-2 block"></i>
                    <p class="font-bold text-slate-800">Orden de Compra Anulada</p>
                    <p class="text-xs text-slate-500 mt-1">Esta orden fue cancelada y no permite recepciones ni movimientos de inventario.</p>
                @endif
            </div>
        @endif
    @endcan

</div>
@endsection
