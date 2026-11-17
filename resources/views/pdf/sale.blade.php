@extends('pdf.layout')

@section('title', 'Factura ' . $sale->invoice_number)

@section('header_doc')
    <div class="doc-badge">
        FACTURA DE VENTA<br>
        <span style="font-size: 13pt;">{{ $sale->invoice_number }}</span>
    </div>
    <div style="margin-top: 6px; font-size: 8pt; color: #475569;">
        <strong>Fecha:</strong> {{ $sale->sale_date ? $sale->sale_date->format('d/m/Y') : '' }}<br>
        <strong>Estado:</strong>
        <span class="badge-status badge-{{ $sale->status }}">{{ strtoupper($sale->status) }}</span><br>
        <strong>Cajero / Asesor:</strong> {{ $sale->user?->name ?? 'Sistema' }}
    </div>
@endsection

@section('content')

    <!-- Datos del Cliente y Facturación -->
    <table style="margin-bottom: 12px;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 6px;">
                <div class="info-box">
                    <div class="info-title">Adquirente / Cliente</div>
                    <strong>{{ $sale->client?->name ?? 'Consumidor Final' }}</strong><br>
                    <span style="color: #64748b;">{{ $sale->client?->document_type ?? 'CC' }}:</span> {{ $sale->client?->document_number ?? '222222222222' }}<br>
                    @if($sale->client?->phone)
                        <span style="color: #64748b;">Tel:</span> {{ $sale->client->phone }}<br>
                    @endif
                    @if($sale->client?->email)
                        <span style="color: #64748b;">Email:</span> {{ $sale->client->email }}<br>
                    @endif
                    @if($sale->client?->address)
                        <span style="color: #64748b;">Dirección:</span> {{ $sale->client->address }}
                    @endif
                </div>
            </td>
            <td style="width: 45%; vertical-align: top; padding-left: 6px;">
                <div class="info-box">
                    <div class="info-title">Información de Operación</div>
                    <strong>Canal de Venta:</strong> Dark Kitchen / Mostrador<br>
                    <span style="color: #64748b;">Forma de Pago:</span> Contado / Transferencia<br>
                    <span style="color: #64748b;">Moneda:</span> COP ($) Peso Colombiano<br>
                    <span style="color: #64748b;">Régimen:</span> Común - Responsable de IVA
                </div>
            </td>
        </tr>
    </table>

    <!-- Tabla de Ítems Vendidos -->
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 6%;">#</th>
                <th class="text-left" style="width: 50%;">Descripción del Producto</th>
                <th class="text-center" style="width: 12%;">Cantidad</th>
                <th class="text-right" style="width: 16%;">Precio Unit.</th>
                <th class="text-right" style="width: 16%;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sale->details as $index => $detail)
                <tr>
                    <td class="text-center font-mono">{{ $index + 1 }}</td>
                    <td class="text-left">
                        <strong>{{ $detail->product?->name ?? 'Producto #' . $detail->product_id }}</strong>
                    </td>
                    <td class="text-center font-mono font-bold">{{ $detail->quantity }}</td>
                    <td class="text-right font-mono">${{ number_format((float) $detail->unit_price, 2, ',', '.') }}</td>
                    <td class="text-right font-mono font-bold">${{ number_format((float) $detail->subtotal, 2, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales y Aspectos Legales -->
    <table style="margin-top: 10px;">
        <tr>
            <td style="width: 55%; vertical-align: top; padding-right: 15px;">
                @if($sale->notes)
                    <div class="info-box">
                        <div class="info-title">Observaciones</div>
                        <p style="margin: 0; font-size: 8pt; color: #475569;">{{ $sale->notes }}</p>
                    </div>
                @endif
                <div style="font-size: 7.5pt; color: #64748b; line-height: 1.35;">
                    <strong>Resolución DIAN:</strong> No. 18764000001 de 2026. Prefijo FV.<br>
                    Rango autorizado: FV-00001 al FV-99999. Vigencia: 12 meses.<br>
                    Esta factura electrónica de venta constituye título valor según la Ley 1231 de 2008.
                    Para reclamos de garantía, conserve este documento soporte.
                </div>
            </td>
            <td style="width: 45%; vertical-align: top;">
                <table class="totals-table">
                    <tr>
                        <td class="text-right">Subtotal Gravable:</td>
                        <td class="text-right font-mono font-bold">${{ number_format((float) $sale->subtotal, 2, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="text-right">IVA (19%):</td>
                        <td class="text-right font-mono font-bold">${{ number_format((float) $sale->tax, 2, ',', '.') }}</td>
                    </tr>
                    <tr class="total-final">
                        <td class="text-right">TOTAL A PAGAR:</td>
                        <td class="text-right font-mono font-bold" style="color: #047857;">
                            ${{ number_format((float) $sale->total, 2, ',', '.') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Firmas / Constancia -->
    <table class="signatures">
        <tr>
            <td style="width: 50%; text-align: center;">
                <div class="signature-line">
                    <strong>QuickFood Dark Kitchen SAS</strong><br>
                    Firma Autorizada
                </div>
            </td>
            <td style="width: 50%; text-align: center;">
                <div class="signature-line">
                    <strong>Firma / Aceptación del Cliente</strong><br>
                    C.C. / NIT: {{ $sale->client?->document_number ?? '____________________' }}
                </div>
            </td>
        </tr>
    </table>

@endsection
