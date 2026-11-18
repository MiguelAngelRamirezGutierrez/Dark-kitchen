@extends('pdf.layout')

@section('title', 'Kardex '.$product->name)
@section('doc_type', 'Kardex de inventario')
@section('doc_number', 'PROD-'.str_pad($product->id, 5, '0', STR_PAD_LEFT))

@section('header_doc')
    <div class="doc-badge">
        <div style="font-size: 8pt; text-transform: uppercase; letter-spacing: 1px; color: #9a3412;">Kardex de Inventario</div>
        <div style="font-size: 13pt; font-weight: bold; font-family: monospace;">PROD-{{ str_pad($product->id, 5, '0', STR_PAD_LEFT) }}</div>
    </div>
@endsection

@section('content')
    <table class="info-box" style="margin-bottom: 15px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                <strong>Producto:</strong> {{ $product->name }}<br>
                <strong>Stock actual:</strong> {{ $product->stock }}
            </td>
            <td style="width: 40%; vertical-align: top;">
                <strong>Periodo:</strong> {{ $from ?: 'inicio' }} a {{ $to ?: 'hoy' }}<br>
                <strong>Entradas:</strong> {{ $totals['entradas'] }} · <strong>Salidas:</strong> {{ $totals['salidas'] }}
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-left">Fecha</th>
                <th class="text-left">Concepto</th>
                <th class="text-left">Documento</th>
                <th class="text-right">Entrada</th>
                <th class="text-right">Salida</th>
                <th class="text-right">Saldo</th>
                <th class="text-right">Valor unit.</th>
                <th class="text-left">Usuario</th>
                <th class="text-left">Notas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($movements as $m)
                <tr>
                    <td>{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $m->conceptLabel() }}</td>
                    <td>{{ $m->referenceLabel() ?? '—' }}</td>
                    <td class="text-right">{{ $m->type === 'entrada' ? $m->quantity : '' }}</td>
                    <td class="text-right">{{ $m->type === 'salida' ? $m->quantity : '' }}</td>
                    <td class="text-right font-bold">{{ $m->balance }}</td>
                    <td class="text-right">{{ $m->unit_cost !== null ? '$'.number_format((float) $m->unit_cost, 2) : '' }}</td>
                    <td>{{ $m->user?->name ?? 'Sistema' }}</td>
                    <td>{{ $m->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
