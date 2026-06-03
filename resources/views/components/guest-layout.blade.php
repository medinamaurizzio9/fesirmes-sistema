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
    @php($institution = \App\Models\SystemSetting::institutional())
    <main class="flex min-h-screen items-center justify-center bg-slate-100 px-4 py-10">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-[84px] w-[84px] items-center justify-center rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm">
                    @if ($institution['system_logo_url'])
                        <img src="{{ $institution['system_logo_url'] }}" alt="Logo FESIRMES" class="h-full w-full object-contain">
                    @else
                        <div class="flex h-full w-full items-center justify-center rounded-lg bg-cyan-800 text-lg font-bold text-white">FE</div>
                    @endif
                </div>
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
