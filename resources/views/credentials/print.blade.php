<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Imprimir credencial - {{ $affiliate->full_name }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page { size: 85.6mm 54mm; margin: 0; }
        @media print {
            body { background: #fff !important; }
            .print-actions { display: none !important; }
            .print-wrap { padding: 0 !important; }
            .credential-preview { box-shadow: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100">
    <div class="print-actions flex justify-center gap-3 p-4">
        <button onclick="window.print()" class="btn-primary">Imprimir</button>
        <a href="{{ route('afiliados.credential.show', $affiliate) }}" class="btn-secondary">Volver</a>
    </div>
    <main class="print-wrap flex min-h-screen items-center justify-center p-4">
        <div class="credential-preview">
            @include('credentials.partials.card')
        </div>
    </main>
    <script>
        window.addEventListener('load', () => window.print());
    </script>
</body>
</html>
