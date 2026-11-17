@extends('layouts.app')

@section('title', 'Nueva Orden de Compra')
@section('page_title', 'Registrar Orden de Compra & Abastecimiento')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8">
        <form method="POST" action="{{ route('purchases.store') }}" id="purchase-form" class="space-y-6">
            @csrf

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <p class="font-bold mb-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Por favor corrija los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Datos Generales de la Orden -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="provider_id" class="block text-sm font-semibold text-slate-700 mb-1">Proveedor *</label>
                    <select id="provider_id" name="provider_id" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
                        <option value="">Seleccione un proveedor</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}" @selected(old('provider_id') == $provider->id)>
                                {{ $provider->name }} — {{ $provider->document_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="purchase_date" class="block text-sm font-semibold text-slate-700 mb-1">Fecha de la Orden *</label>
                    <input id="purchase_date" name="purchase_date" type="date"
                           value="{{ old('purchase_date', now()->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm" required>
                </div>

                <div>
                    <label for="provider_invoice" class="block text-sm font-semibold text-slate-700 mb-1">Factura del Proveedor (Opcional)</label>
                    <input id="provider_invoice" name="provider_invoice" type="text"
                           value="{{ old('provider_invoice') }}"
                           placeholder="Ej: FE-98234"
                           class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">
                </div>
            </div>

            <!-- Tabla Dinámica de Productos a Comprar -->
            <div class="space-y-3 pt-4 border-t border-slate-100">
                <div class="flex justify-between items-center">
                    <label class="block text-sm font-bold text-slate-800">Insumos y Productos a Comprar *</label>
                    <span class="text-xs text-slate-400">Se pueden seleccionar productos con stock en 0 para reabastecer</span>
                </div>

                <div class="overflow-x-auto rounded-xl border border-slate-200">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-600 uppercase">
                            <tr>
                                <th class="p-3">Producto / Insumo</th>
                                <th class="p-3 w-28 text-center">Cantidad</th>
                                <th class="p-3 w-48 text-right">Costo Unitario ($)</th>
                                <th class="p-3 w-36 text-right">Subtotal</th>
                                <th class="p-3 w-16 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body" class="divide-y divide-slate-100">
                            <!-- Filas dinámicas generadas con JS -->
                        </tbody>
                    </table>
                </div>

                <button type="button" id="add-item" class="inline-flex items-center space-x-2 text-orange-600 hover:text-orange-700 font-semibold text-xs mt-2 transition">
                    <i class="fa-solid fa-plus-circle text-sm"></i>
                    <span>+ Agregar otro producto</span>
                </button>
            </div>

            <!-- Desglose de Totales -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 border-t border-slate-100 pt-6 bg-slate-50 p-6 rounded-2xl">
                <div>
                    <span class="block text-xs font-semibold uppercase text-slate-500">Subtotal</span>
                    <p id="subtotal-display" class="text-xl font-mono font-bold text-slate-800">$0.00</p>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase text-slate-500">IVA (19%)</span>
                    <p id="tax-display" class="text-xl font-mono font-bold text-slate-800">$0.00</p>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase text-slate-500">Total a Pagar</span>
                    <p id="total-display" class="text-2xl font-mono font-black text-emerald-600">$0.00</p>
                </div>
            </div>

            <!-- Notas -->
            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1">Notas u Observaciones (Opcional)</label>
                <textarea id="notes" name="notes" rows="2"
                          placeholder="Instrucciones de despacho, bodega de entrega o condiciones de crédito..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 text-sm shadow-sm">{{ old('notes') }}</textarea>
            </div>

            <!-- Botones de Acción -->
            <div class="flex items-center justify-end space-x-3 pt-6 border-t border-slate-100">
                <a href="{{ route('purchases.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-sm font-semibold transition">
                    Cancelar
                </a>
                <button type="submit" class="bg-gradient-to-r from-orange-600 to-amber-500 hover:from-orange-700 hover:to-amber-600 text-white text-sm font-semibold px-6 py-2.5 rounded-xl shadow-sm transition">
                    <i class="fa-solid fa-file-invoice mr-1.5"></i>Registrar Orden de Compra
                </button>
            </div>
        </form>
    </div>

</div>

<!-- Plantilla de Fila para JavaScript -->
<template id="item-row-template">
    <tr class="item-row hover:bg-slate-50/50 transition">
        <td class="p-3">
            <select name="items[__INDEX__][product_id]" class="product-select w-full px-3 py-2 rounded-lg border border-slate-200 text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" required>
                <option value="">Seleccione un producto</option>
                @foreach ($products as $product)
                    <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                        {{ $product->name }} (Stock actual: {{ $product->stock }})
                    </option>
                @endforeach
            </select>
        </td>
        <td class="p-3">
            <input type="number" name="items[__INDEX__][quantity]" value="1" min="1"
                   class="quantity-input w-full px-3 py-2 rounded-lg border border-slate-200 text-center text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" required>
        </td>
        <td class="p-3">
            <input type="number" name="items[__INDEX__][unit_cost]" value="" min="0.01" step="0.01" placeholder="0.00"
                   class="cost-input w-full px-3 py-2 rounded-lg border border-slate-200 text-right text-xs focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 font-mono" required>
            <span class="price-hint block text-[11px] text-slate-500 mt-1"></span>
        </td>
        <td class="p-3 text-right font-mono font-bold text-slate-900">
            <span class="subtotal-display">$0.00</span>
        </td>
        <td class="p-3 text-center">
            <button type="button" class="remove-row text-rose-500 hover:text-rose-700 transition p-1">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        </td>
    </tr>
</template>

@push('scripts')
<script>
    let index = 0;
    const itemsBody = document.getElementById('items-body');
    const template = document.getElementById('item-row-template').innerHTML;
    const addBtn = document.getElementById('add-item');

    // Filas enviadas antes (si el servidor devolvió un error, se reconstruyen)
    const oldItems = @json(array_values(old('items', [])));

    function addRow(item = null) {
        const html = template.replace(/__INDEX__/g, index++);
        itemsBody.insertAdjacentHTML('beforeend', html);
        const row = itemsBody.lastElementChild;

        if (item) {
            row.querySelector('.product-select').value = item.product_id ?? '';
            row.querySelector('.quantity-input').value = item.quantity ?? 1;
            row.querySelector('.cost-input').value = item.unit_cost ?? '';
        }

        bindRow(row);
        recalculate();
    }

    function bindRow(row) {
        row.querySelector('.product-select').addEventListener('change', recalculate);
        row.querySelector('.quantity-input').addEventListener('input', recalculate);
        row.querySelector('.cost-input').addEventListener('input', recalculate);
        row.querySelector('.remove-row').addEventListener('click', () => {
            row.remove();
            recalculate();
        });
    }

    function formatCOP(value) {
        return '$ ' + Number(value).toLocaleString('es-CO', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function recalculate() {
        let subtotal = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const select = row.querySelector('.product-select');
            const opt = select.options[select.selectedIndex];
            const salePrice = parseFloat(opt?.dataset?.price || 0);
            const qty = parseInt(row.querySelector('.quantity-input').value || 0);
            const cost = parseFloat(row.querySelector('.cost-input').value || 0);
            const lineSubtotal = cost * qty;

            // Ayuda visual: precio de venta actual del producto (el costo debería ser menor)
            const hint = row.querySelector('.price-hint');
            if (salePrice > 0) {
                hint.textContent = 'Precio venta: ' + formatCOP(salePrice);
                if (cost > 0 && cost >= salePrice) {
                    hint.textContent += ' ⚠️ Costo ≥ Precio';
                    hint.className = 'price-hint block text-[11px] font-bold text-rose-600 mt-1';
                } else {
                    hint.className = 'price-hint block text-[11px] text-slate-500 mt-1';
                }
            } else {
                hint.textContent = '';
            }

            row.querySelector('.subtotal-display').textContent = formatCOP(lineSubtotal);
            subtotal += lineSubtotal;
        });

        const tax = subtotal * 0.19;
        const total = subtotal + tax;

        document.getElementById('subtotal-display').textContent = formatCOP(subtotal);
        document.getElementById('tax-display').textContent = formatCOP(tax);
        document.getElementById('total-display').textContent = formatCOP(total);
    }

    addBtn.addEventListener('click', () => addRow());

    if (oldItems.length) {
        oldItems.forEach(item => addRow(item));
    } else {
        addRow(); // Fila inicial
    }
</script>
@endpush
@endsection
