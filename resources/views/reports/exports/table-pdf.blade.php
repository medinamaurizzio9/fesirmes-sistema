<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    @php
        $columnCount = count($headers);
        $tableFontSize = $columnCount >= 9 ? '6.8px' : ($columnCount >= 7 ? '7.4px' : '8.2px');
        $cellPadding = $columnCount >= 8 ? '3px 4px' : '4px 5px';
    @endphp
    <style>
        @page {
            margin: 15mm 10mm 14mm 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #0f172a;
            line-height: 1.25;
        }

        .document-header {
            border-bottom: 2px solid #155e75;
            padding-bottom: 9px;
            margin-bottom: 9px;
        }

        .brand-table {
            width: 100%;
            border-collapse: collapse;
        }

        .brand-table td {
            border: 0;
            padding: 0;
        }

        .logo-cell {
            width: 58px;
            vertical-align: middle;
        }

        .logo-box {
            width: 48px;
            height: 48px;
            border: 1px solid #cbd5e1;
            border-radius: 4px;
            text-align: center;
            vertical-align: middle;
            background: #ffffff;
        }

        .logo-box img {
            max-width: 42px;
            max-height: 42px;
            margin-top: 3px;
        }

        .title-cell {
            vertical-align: middle;
            padding-left: 8px;
        }

        h1 {
            margin: 0;
            color: #0f172a;
            font-size: 19px;
            line-height: 1.12;
            letter-spacing: .2px;
        }

        .subtitle {
            margin-top: 3px;
            color: #155e75;
            font-size: 10px;
            font-weight: bold;
        }

        .meta {
            margin-top: 5px;
            color: #475569;
            font-size: 8px;
        }

        .filters {
            margin: 8px 0 10px;
            padding: 7px 8px;
            border: 1px solid #d8e1e8;
            border-left: 4px solid #155e75;
            background: #f8fafc;
            color: #334155;
            font-size: 8px;
        }

        .filters-title {
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .filter-item {
            display: inline-block;
            margin: 1px 8px 1px 0;
            white-space: normal;
        }

        .muted {
            color: #64748b;
        }

        .table-wrap {
            width: 100%;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: {{ $tableFontSize }};
        }

        .report-table thead {
            display: table-header-group;
        }

        .report-table tr {
            page-break-inside: avoid;
        }

        .report-table th {
            padding: {{ $cellPadding }};
            border: 1px solid #b6c3cf;
            background: #155e75;
            color: #ffffff;
            text-align: left;
            font-weight: bold;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .report-table td {
            padding: {{ $cellPadding }};
            border: 1px solid #d6dee6;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        .report-table tbody tr:nth-child(even) td {
            background: #f8fafc;
        }

        .numeric,
        .report-table td:nth-last-child(-n+3) {
            text-align: center;
        }

        .footer {
            position: fixed;
            left: 0;
            right: 0;
            bottom: -8mm;
            height: 9mm;
            border-top: 1px solid #cbd5e1;
            padding-top: 3px;
            color: #64748b;
            font-size: 8px;
        }

        .footer-left {
            float: left;
        }

        .footer-right {
            float: right;
        }

        .page-number:after {
            content: "Pagina " counter(page) " de " counter(pages);
        }
    </style>
</head>
<body>
    <div class="footer">
        <span class="footer-left">Sistema Institucional FESIRMES · {{ $year }}</span>
        <span class="footer-right page-number"></span>
    </div>

    <header class="document-header">
        <table class="brand-table">
            <tr>
                <td class="logo-cell">
                    <div class="logo-box">
                        @if ($logoDataUri)
                            <img src="{{ $logoDataUri }}" alt="Logo FESIRMES">
                        @else
                            <div style="padding-top:15px;font-weight:bold;color:#155e75;">FES</div>
                        @endif
                    </div>
                </td>
                <td class="title-cell">
                    <h1>{{ $title }}</h1>
                    <div class="subtitle">Federacion Sindical de Ramas Medicas de Salud Publica</div>
                    <div class="meta">
                        Generado: {{ $generatedAt->format('d/m/Y H:i') }}
                        &nbsp;|&nbsp; Usuario: {{ $generatedBy ?? 'Sistema' }}
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <section class="filters">
        <div class="filters-title">Filtros aplicados</div>
        @forelse ($filters as $key => $value)
            <span class="filter-item"><strong>{{ str_replace('_', ' ', $key) }}:</strong> {{ $value }}</span>
        @empty
            <span class="muted">Sin filtros aplicados</span>
        @endforelse
    </section>

    <main class="table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    @foreach ($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @forelse ($rows as $row)
                    <tr>
                        @foreach ($row as $cell)
                            <td>{{ $cell }}</td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($headers) }}" class="muted">Sin registros para los filtros seleccionados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </main>
</body>
</html>
