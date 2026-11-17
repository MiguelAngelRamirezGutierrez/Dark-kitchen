@extends('pdf.layout')

@section('title', 'Orden de Compra ' . $purchase->purchase_number)

@section('header_doc')
    <div class="doc-badge">
        ORDEN DE COMPRA<br>
        <span style="font-size: 13pt;">{{ $purchase->purchase_number }}</span>
    </div>
    <div style="margin-top: 6px; font-size: 8pt; color: #475569;">
        <strong>Fecha Emisión:</strong> {{ $purchase->purchase_date ? $purchase->purchase_date->format('d/m/Y') : '' }}<br>
        <strong>Estado:</strong>
        <span class="badge-status badge-{{ $purchase->status }}">{{ strtoupper($purchase->status) }}</span><br>
        @if($purchase->provider_invoice)
            <strong>Factura Ref:</strong> {{ $purchase->provider_invoice }}<br>
        @endif
        <strong>Registrado por:</strong> {{ $purchase->user?->name ?? 'Sistema' }}
    </div>
@endsection

@section('content')

    <!-- Datos del Proveedor y de Entrega -->
    <table style="margin-bottom: 12px;">
        <tr>
            <td style="width: 50%; vertical-align: top; padding-right: 6px;">
                <div class="info-box">
                    <div class="info-title">Datos del Proveedor</div>
                    <strong>{{ $purchase->provider?->name }}</strong><br>
                    <span style="color: #64748b;">{{ $purchase->provider?->document_type }}:</span> {{ $purchase->provider?->document_number }}<br>
                    @if($purchase->provider?->contact_name)
                        <span style="color: #64748b;">Contacto:</span> {{ $purchase->provider->contact_name }}<br>
                    @endif
                    @if($purchase->provider?->phone)
                        <span style="color: #64748b;">Tel:</span> {{ $purchase->provider->phone }}<br>
                    @endif
                    @if($purchase->provider?->email)
                        <span style="color: #64748b;">Email:</span> {{ $purchase->provider->email }}<br>
                    @endif
                    @if($purchase->provider?->address)
                        <span style="color: #64748b;">Dir:</span> {{ $purchase->provider->address }}
                    @endif
                </div>
            </td>
            <td style="width: 50%; vertical-align: top; padding-left: 6px;">
                <div class="info-box">
                    <div class="info-title">Destino de Entrega & Recepción</div>
                    <strong>QuickFood Dark Kitchen - Bodega Central</strong><br>
                    <span style="color: #64748b;">Dirección:</span> Cra 5 # 10-25, Cartago, Valle<br>
                    <span style="color: #64748b;">Horario Recepción:</span> Lun - Sáb 07:00 a 16:00<br>
                    <span style="color: #64748b;">Condición de Pago:</span> Contraentrega / Crédito 15 días<br>
                    <span style="color: #64748b;">Moneda:</span> COP ($) Peso Colombiano
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de Ítems Solicitados -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 6%;">#</th>
                <th class="text-left" style="width: 44%;">Insumo / Producto</th>
                <th class="text-center" style="width: 12%;">Solicitado</th>
                <th class="text-center" style="width: 12%;">Recibido</th>
                <th class="text-right" style="width: 13%;">Costo Unit.</th>
                <th class="text-right" style="width: 13%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->details as $index => $detail)
                <tr>
                    <td class="text-center font-mono">{{ $index + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $detail->product?->name ?? 'Producto #' . $detail->product_id }}</strong>
                    </td>
                    <td class="text-center font-mono font-bold">{{ $detail->quantity }}</td>
                    <td class="text-center font-mono" style="color: {{ $detail->received_quantity > 0 ? '#166534' : '#64748b' }};">
                        {{ $detail->received_quantity }}
                    </td>
                    <td class="text-right font-mono">${{ number_format((float) $detail->unit_cost, 2, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold">${{ number_format((float) $detail->subtotal, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales y Observaciones -->
    <table style="margin-top: 10px;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                @if($purchase->notes)
                    <div class="info-box">
                        <div class="info-title">Observaciones / Instrucciones de Envío</div>
                        <p style="margin: 0; font-size: 8pt; color: #475569;">{{ $purchase->notes }}</p>
                    </div>
                @endif
                <div style="font-size: 7.5pt; color: #64748b; line-height: 1.3;">
                    <strong>Políticas de Recepción:</strong><br>
                    1. La mercancía debe venir acompañada de la factura o remisión física correspondiente.<br>
                    2. Los insumos perecederos y carnes deben cumplir con la cadena de frío certificada.<br>
                    3. No se aceptarán entregas que excedan las cantidades pactadas en esta orden.
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td class="text-right">Subtotal Neto:</td>
                        <td class="text-right font-mono font-bold">${{ number_format((float) $purchase->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right">IVA (19%):</td>
                        <td class="text-right font-mono font-bold">${{ number_format((float) $purchase->tax, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="total-final">
                        <td class="text-right">TOTAL ORDEN:</td>
                        <td class="text-right font-mono font-bold" style="color: #c2410c;">
                            ${{ number_format((float) $purchase->total, 2, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Firmas -->
    <table class="signatures">
        <tr>
            <td style="width: 50%; text-align: center;">
                <div class="signature-line">
                    <strong>{{ $purchase->user?->name ?? 'Responsable de Compras' }}</strong><br>
                    Departamento de Compras & Suministros
                </div>
            </td>
            <td style="width: 50%; text-align: center;">
                <div class="signature-line">
                    <strong>Firma y Sello de Proveedor</strong><br>
                    Aceptación de Términos y Despacho
                </div>
            </td>
        </tr>
    </table>

@endsection
