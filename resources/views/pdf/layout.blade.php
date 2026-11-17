<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Documento - QuickFood ERP')</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            color: #1e293b;
            line-height: 1.4;
            background-color: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            margin-bottom: 20px;
            border-bottom: 2px solid #ea580c;
            padding-bottom: 12px;
        }

        .brand-title {
            font-size: 16pt;
            font-weight: bold;
            color: #ea580c;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .brand-subtitle {
            font-size: 8pt;
            color: #64748b;
            margin: 2px 0 0 0;
        }

        .doc-badge {
            background-color: #fff7ed;
            border: 1.5px solid #fdba74;
            color: #c2410c;
            font-size: 11pt;
            font-weight: bold;
            padding: 6px 12px;
            text-align: right;
            display: inline-block;
            border-radius: 4px;
        }

        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 10px 12px;
            margin-bottom: 15px;
        }

        .info-title {
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #64748b;
            margin-bottom: 6px;
            border-bottom: 1px solid #cbd5e1;
            padding-bottom: 2px;
        }

        .data-table {
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .data-table th {
            background-color: #f1f5f9;
            color: #334155;
            font-size: 8pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 7px 8px;
            border-bottom: 1.5px solid #cbd5e1;
        }

        .data-table td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 8.5pt;
        }

        .data-table tr:nth-child(even) td {
            background-color: #fafafa;
        }

        .text-left { text-align: left; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'DejaVu Sans Mono', monospace; }
        .font-bold { font-weight: bold; }

        .totals-table {
            width: 260px;
            margin-left: auto;
            margin-top: 10px;
        }

        .totals-table td {
            padding: 4px 6px;
            font-size: 8.5pt;
        }

        .total-final {
            border-top: 2px solid #0f172a;
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
        }

        .footer {
            position: fixed;
            bottom: -12mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7.5pt;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }

        .signatures {
            margin-top: 40px;
            width: 100%;
        }

        .signature-line {
            border-top: 1px solid #64748b;
            padding-top: 4px;
            font-size: 8pt;
            color: #475569;
            text-align: center;
            width: 80%;
            margin: 0 auto;
        }

        .badge-status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-recibida { background: #dcfce7; color: #166534; }
        .badge-parcial { background: #fef3c7; color: #92400e; }
        .badge-pendiente { background: #e0f2fe; color: #075985; }
        .badge-anulada { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <h1 class="brand-title">QuickFood ERP</h1>
                <p class="brand-subtitle"><strong>Dark Kitchen & Gestión de Alimentos SAS</strong></p>
                <p class="brand-subtitle">NIT: 900.123.456-7 · Régimen Responsable de IVA</p>
                <p class="brand-subtitle">Cartago, Valle del Cauca · Tel: (+57) 310 123 4567</p>
                <p class="brand-subtitle">contacto@quickfood.internal</p>
            </td>
            <td style="width: 45%; vertical-align: top; text-align: right;">
                @yield('header_doc')
            </td>
        </tr>
    </table>

    @yield('content')

    <div class="footer">
        QuickFood ERP · Generado el {{ now()->format('d/m/Y H:i:s') }} · Documento generado electrónicamente válido para efectos administrativos y contables.
    </div>

</body>
</html>
