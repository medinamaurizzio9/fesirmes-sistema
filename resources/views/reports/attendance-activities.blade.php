<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Asistencia por actividad</h1>
            </div>
            <a href="{{ route('reportes.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('reportes.attendance.activities') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 lg:grid-cols-3 xl:grid-cols-6">
            <div>
                <label class="input-label" for="activity_id">Actividad</label>
                <select id="activity_id" name="activity_id" class="input-field">
                    <option value="">Todas</option>
                    @foreach ($activityOptions as $option)
                        <option value="{{ $option->id }}" @selected((int) request('activity_id') === $option->id)>{{ $option->fecha->format('d/m/Y') }} - {{ $option->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="input-label" for="estado_actividad">Estado actividad</label>
                <select id="estado_actividad" name="estado_actividad" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($activityStatuses as $status)
                        <option value="{{ $status }}" @selected(request('estado_actividad') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="input-label" for="fecha_desde">Desde</label>
                <input id="fecha_desde" name="fecha_desde" type="date" value="{{ request('fecha_desde') }}" class="input-field">
            </div>
            <div>
                <label class="input-label" for="fecha_hasta">Hasta</label>
                <input id="fecha_hasta" name="fecha_hasta" type="date" value="{{ request('fecha_hasta') }}" class="input-field">
            </div>
            <div>
                <label class="input-label" for="sindicato_id">Sindicato</label>
                <select id="sindicato_id" name="sindicato_id" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($sindicatos as $sindicato)
                        <option value="{{ $sindicato->id }}" @selected((int) request('sindicato_id') === $sindicato->id)>{{ $sindicato->sigla ?? $sindicato->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="input-label" for="tipo_item">Tipo item</label>
                <select id="tipo_item" name="tipo_item" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($itemTypes as $type)
                        <option value="{{ $type }}" @selected(request('tipo_item') === $type)>{{ $type }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button class="btn-primary" type="submit">Filtrar</button>
            <a href="{{ route('reportes.attendance.activities') }}" class="btn-ghost">Limpiar</a>
            @if ($canExport)
                <a href="{{ route('reportes.attendance.activities.csv', request()->query()) }}" class="btn-secondary">Exportar CSV</a>
                <a href="{{ route('reportes.attendance.activities.pdf', request()->query()) }}" class="btn-secondary">Exportar PDF</a>
            @endif
        </div>
    </form>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Actividad</th>
                        <th class="px-5 py-3">Fecha</th>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3">Activos</th>
                        <th class="px-5 py-3">Validos</th>
                        <th class="px-5 py-3">Duplicados</th>
                        <th class="px-5 py-3">Observados</th>
                        <th class="px-5 py-3">Invalidos</th>
                        <th class="px-5 py-3">Asistencia</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $activity->nombre }}</td>
                            <td class="px-5 py-4">{{ $activity->fecha->format('d/m/Y') }}</td>
                            <td class="px-5 py-4 capitalize">{{ $activity->estado }}</td>
                            <td class="px-5 py-4">{{ $activeAffiliates }}</td>
                            <td class="px-5 py-4">{{ $activity->validos }}</td>
                            <td class="px-5 py-4">{{ $activity->duplicados }}</td>
                            <td class="px-5 py-4">{{ $activity->observados }}</td>
                            <td class="px-5 py-4">{{ $activity->invalidos }}</td>
                            <td class="px-5 py-4">{{ $activeAffiliates > 0 ? round(($activity->validos / $activeAffiliates) * 100, 2) : 0 }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="px-5 py-10 text-center text-slate-500">Sin actividades registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $activities->links() }}</div>
    </div>
</x-app-layout>
