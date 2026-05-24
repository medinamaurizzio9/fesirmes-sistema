<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Actividad</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $activity->nombre }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $activity->fecha->format('d/m/Y') }} - {{ $activity->lugar ?? 'Sin lugar' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('actividades.index') }}" class="btn-secondary">Volver</a>
                <a href="{{ route('actividades.asistencias.index', $activity) }}" class="btn-secondary">Ver asistencia</a>
                <a href="{{ route('actividades.asistencias.report', $activity) }}" class="btn-secondary">Reporte</a>
                @if (auth()->user()->role->canManageAffiliates())
                    <a href="{{ route('actividades.asistencias.import.form', $activity) }}" class="btn-primary">Importar CSV</a>
                    <a href="{{ route('actividades.edit', $activity) }}" class="btn-secondary">Editar</a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <section class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4"><h2 class="section-title">Detalle</h2></div>
            <dl class="grid gap-0 divide-y divide-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                <div class="p-5"><dt class="text-sm text-slate-500">Estado</dt><dd class="mt-1 font-semibold capitalize">{{ $activity->estado }}</dd></div>
                <div class="p-5"><dt class="text-sm text-slate-500">Horario</dt><dd class="mt-1 font-semibold">{{ $activity->hora_inicio }} - {{ $activity->hora_fin ?? 'Sin fin' }}</dd></div>
            </dl>
            <div class="border-t border-slate-100 p-5">
                <div class="text-sm text-slate-500">Descripcion</div>
                <p class="mt-1 whitespace-pre-line text-slate-950">{{ $activity->descripcion ?? 'Sin descripcion' }}</p>
            </div>
        </section>
        <aside class="panel h-fit p-5">
            <div class="text-sm text-slate-500">Asistencias validas</div>
            <div class="mt-2 text-4xl font-bold text-slate-950">{{ $activity->valid_attendances_count }}</div>
            <div class="mt-4 text-sm text-slate-500">Observaciones/errores: {{ $activity->invalid_attendances_count }}</div>
            @if (auth()->user()->role->canModifyCi() && $activity->estado !== 'cancelada')
                <form method="POST" action="{{ route('actividades.destroy', $activity) }}" class="mt-5" onsubmit="return confirm('Se marcara como cancelada.');">
                    @csrf
                    @method('DELETE')
                    <button class="btn-secondary w-full">Cancelar actividad</button>
                </form>
            @endif
        </aside>
    </div>
</x-app-layout>
