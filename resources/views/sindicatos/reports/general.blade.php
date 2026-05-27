<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Afiliados por sindicato</h1>
            </div>
            <a href="{{ route('sindicatos.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Sindicato</th>
                        <th class="px-5 py-3">Sigla</th>
                        <th class="px-5 py-3">Total</th>
                        <th class="px-5 py-3">Activos</th>
                        <th class="px-5 py-3">Bajas</th>
                        <th class="px-5 py-3">Suspendidos</th>
                        <th class="px-5 py-3">Observados</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($sindicatos as $sindicato)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $sindicato->nombre }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $sindicato->sigla ?? 'Sin sigla' }}</td>
                            <td class="px-5 py-4">{{ $sindicato->total_afiliados }}</td>
                            <td class="px-5 py-4">{{ $sindicato->activos }}</td>
                            <td class="px-5 py-4">{{ $sindicato->bajas }}</td>
                            <td class="px-5 py-4">{{ $sindicato->suspendidos }}</td>
                            <td class="px-5 py-4">{{ $sindicato->observados }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
