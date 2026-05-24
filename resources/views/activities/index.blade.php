<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">FASE 3</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Actividades</h1>
                <p class="mt-1 text-sm text-slate-600">Eventos y control de asistencia por CSV desde QR.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('actividades.report.general') }}" class="btn-secondary">Reporte general</a>
                @if (auth()->user()->role->canManageAffiliates())
                    <a href="{{ route('actividades.create') }}" class="btn-primary">Nueva actividad</a>
                @endif
            </div>
        </div>
    </x-slot>

    <form method="GET" class="panel mb-6 grid gap-4 p-4 md:grid-cols-[1fr_220px_auto]">
        <div>
            <label class="input-label" for="buscar">Buscar</label>
            <input id="buscar" name="buscar" value="{{ request('buscar') }}" class="input-field" placeholder="Nombre o lugar">
        </div>
        <div>
            <label class="input-label" for="estado">Estado</label>
            <select id="estado" name="estado" class="input-field">
                <option value="">Todos</option>
                @foreach (['programada', 'realizada', 'cancelada'] as $estado)
                    <option value="{{ $estado }}" @selected(request('estado') === $estado)>{{ ucfirst($estado) }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end">
            <button class="btn-primary w-full">Filtrar</button>
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
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($activities as $activity)
                        <tr>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-950">{{ $activity->nombre }}</div>
                                <div class="text-xs text-slate-500">{{ $activity->lugar ?? 'Sin lugar' }}</div>
                            </td>
                            <td class="px-5 py-4">{{ $activity->fecha->format('d/m/Y') }} {{ $activity->hora_inicio }}</td>
                            <td class="px-5 py-4"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold capitalize">{{ $activity->estado }}</span></td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('actividades.show', $activity) }}" class="btn-secondary">Ver</a>
                                    @if (auth()->user()->role->canManageAffiliates())
                                        <a href="{{ route('actividades.edit', $activity) }}" class="btn-secondary">Editar</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-10 text-center text-slate-500">Sin actividades registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $activities->links() }}</div>
    </div>
</x-app-layout>
