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
    <main class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-cyan-800 text-base font-bold text-white shadow-sm">FE</div>
                <h1 class="mt-4 text-3xl font-bold tracking-tight text-slate-950">FESIRMES</h1>
                <p class="mt-2 text-sm text-slate-600">Sistema institucional de afiliados</p>
            </div>

            <div class="panel overflow-hidden">
                {{ $slot }}
            </div>
        </div>
    </main>
</body>
</html>
