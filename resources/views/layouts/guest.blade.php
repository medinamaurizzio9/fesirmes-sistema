<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'FESIRMES') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <h1 class="text-3xl font-bold tracking-tight text-slate-950">FESIRMES</h1>
                <p class="mt-2 text-sm text-slate-600">Sistema de afiliados - Fase 1</p>
            </div>

            <div class="panel p-6">
                {{ $slot }}
            </div>
        </div>
    </main>
</body>
</html>
