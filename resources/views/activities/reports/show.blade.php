<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte de asistencia</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $activity->nombre }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $activity->fecha->format('d/m/Y') }}</p>
            </div>
            <a href="{{ route('actividades.asistencias.export', ['activity' => $activity, 'sindicato_id' => request('sindicato_id')]) }}" class="btn-primary">Exportar reporte</a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('actividades.asistencias.report', $activity) }}" class="panel mb-6 p-4">
        <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-end">
            <div>
                <label class="input-label" for="sindicato_id">Filtrar por sindicato</label>
                <select id="sindicato_id" name="sindicato_id" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($sindicatos as $sindicato)
                        <option value="{{ $sindicato->id }}" @selected((int) request('sindicato_id') === $sindicato->id)>{{ $sindicato->sigla ?? $sindicato->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary" type="submit">Filtrar</button>
            <a href="{{ route('actividades.asistencias.report', $activity) }}" class="btn-ghost">Limpiar</a>
        </div>
    </form>

    <section class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="panel p-5"><div class="text-sm text-slate-500">Afiliados activos</div><div class="mt-2 text-3xl font-bold">{{ $totalActivos }}</div></div>
        <div class="panel p-5"><div class="text-sm text-slate-500">Asistentes validos</div><div class="mt-2 text-3xl font-bold">{{ $validos->count() }}</div></div>
        <div class="panel p-5"><div class="text-sm text-slate-500">Porcentaje</div><div class="mt-2 text-3xl font-bold">{{ $porcentaje }}%</div></div>
    </section>

    <div class="grid gap-6 lg:grid-cols-2">
        <section class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4"><h2 class="section-title">Asistentes</h2></div>
            <div class="divide-y divide-slate-100">
                @forelse ($validos as $attendance)
                    <div class="px-5 py-3 text-sm">{{ $attendance->affiliate?->full_name }} - {{ $attendance->ci_detectado }} - {{ $attendance->affiliate?->sindicato?->sigla ?? $attendance->affiliate?->sindicato?->nombre ?? 'Sin sindicato' }}</div>
                @empty
                    <div class="px-5 py-8 text-sm text-slate-500">Sin asistentes validos.</div>
                @endforelse
            </div>
        </section>
        <section class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4"><h2 class="section-title">Invalidos / observados</h2></div>
            <div class="divide-y divide-slate-100">
                @forelse ($revisiones as $attendance)
                    <div class="px-5 py-3 text-sm"><strong>{{ $attendance->ci_detectado }}</strong> - {{ $attendance->estado }} - {{ $attendance->observacion }}</div>
                @empty
                    <div class="px-5 py-8 text-sm text-slate-500">Sin registros para revision.</div>
                @endforelse
            </div>
        </section>
    </div>
</x-app-layout>
