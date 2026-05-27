<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Historico de asistencia por afiliado</h1>
                <p class="mt-1 text-sm text-slate-600">Solo cuenta actividades realizadas.</p>
            </div>
            <a href="{{ route('reportes.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('reportes.attendance.history') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 lg:grid-cols-3 xl:grid-cols-6">
            <div>
                <label class="input-label" for="buscar">Nombre o C.I.</label>
                <input id="buscar" name="buscar" value="{{ request('buscar') }}" class="input-field" placeholder="Buscar">
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
            <div>
                <label class="input-label" for="estado">Estado afiliado</label>
                <select id="estado" name="estado" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('estado') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="input-label" for="porcentaje_min">Porcentaje min.</label>
                <input id="porcentaje_min" name="porcentaje_min" type="number" min="0" max="100" step="0.01" value="{{ request('porcentaje_min') }}" class="input-field">
            </div>
            <div>
                <label class="input-label" for="porcentaje_max">Porcentaje max.</label>
                <input id="porcentaje_max" name="porcentaje_max" type="number" min="0" max="100" step="0.01" value="{{ request('porcentaje_max') }}" class="input-field">
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button class="btn-primary" type="submit">Filtrar</button>
            <a href="{{ route('reportes.attendance.history') }}" class="btn-ghost">Limpiar</a>
            @if ($canExport)
                <a href="{{ route('reportes.attendance.history.csv', request()->query()) }}" class="btn-secondary">Exportar CSV</a>
                <a href="{{ route('reportes.attendance.history.pdf', request()->query()) }}" class="btn-secondary">Exportar PDF</a>
            @endif
        </div>
    </form>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Nombre</th>
                        <th class="px-5 py-3">C.I.</th>
                        <th class="px-5 py-3">Item</th>
                        <th class="px-5 py-3">Sindicato</th>
                        <th class="px-5 py-3">Actividades realizadas</th>
                        <th class="px-5 py-3">Asistencias validas</th>
                        <th class="px-5 py-3">Porcentaje</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($affiliates as $affiliate)
                        @php
                            $validas = $validCounts[$affiliate->id] ?? 0;
                            $porcentaje = $totalRealizadas > 0 ? round(($validas / $totalRealizadas) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $affiliate->full_name }}</td>
                            <td class="px-5 py-4">{{ $affiliate->ci }}</td>
                            <td class="px-5 py-4">{{ $affiliate->item_principal ?? 'Sin item' }}</td>
                            <td class="px-5 py-4">{{ $affiliate->sindicato?->sigla ?? $affiliate->sindicato?->nombre ?? 'Sin sindicato' }}</td>
                            <td class="px-5 py-4">{{ $totalRealizadas }}</td>
                            <td class="px-5 py-4">{{ $validas }}</td>
                            <td class="px-5 py-4">{{ $porcentaje }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-10 text-center text-slate-500">Sin afiliados registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $affiliates->links() }}</div>
    </div>
</x-app-layout>
