<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte de asistencia</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Asistencia por sindicato</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $activity?->nombre ?? 'Selecciona una actividad realizada' }}</p>
            </div>
            <a href="{{ route('sindicatos.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('sindicatos.report.attendance') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-end">
            <div>
                <label class="input-label" for="activity_id">Actividad realizada</label>
                <select id="activity_id" name="activity_id" class="input-field">
                    <option value="">Seleccionar</option>
                    @foreach ($activities as $option)
                        <option value="{{ $option->id }}" @selected($activity?->id === $option->id)>{{ $option->fecha->format('d/m/Y') }} - {{ $option->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary" type="submit">Ver reporte</button>
        </div>
    </form>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Sindicato</th>
                        <th class="px-5 py-3">Activos</th>
                        <th class="px-5 py-3">Asistentes validos</th>
                        <th class="px-5 py-3">Porcentaje</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @foreach ($sindicatos as $sindicato)
                        @php
                            $validos = $activity ? ($validCounts[$sindicato->id] ?? collect())->count() : 0;
                            $porcentaje = $sindicato->activos > 0 ? round(($validos / $sindicato->activos) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $sindicato->nombre }}</td>
                            <td class="px-5 py-4">{{ $sindicato->activos }}</td>
                            <td class="px-5 py-4">{{ $validos }}</td>
                            <td class="px-5 py-4">{{ $porcentaje }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
