<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Resumen de importacion</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $activity->nombre }}</h1>
            </div>
            <a href="{{ route('actividades.asistencias.index', $activity) }}" class="btn-primary">Ver asistencia</a>
        </div>
    </x-slot>

    <section class="mb-6 grid gap-4 sm:grid-cols-5">
        @foreach (['total' => 'Filas', 'validos' => 'Validas', 'duplicados' => 'Duplicados', 'observados' => 'Observados', 'invalidos' => 'Invalidos'] as $key => $label)
            <div class="panel p-5"><div class="text-sm text-slate-500">{{ $label }}</div><div class="mt-2 text-3xl font-bold">{{ $summary[$key] }}</div></div>
        @endforeach
    </section>

    <div class="panel overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
            <h2 class="section-title">Errores y observaciones</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($summary['errores'] as $error)
                <div class="px-5 py-3 text-sm"><strong>{{ $error['ci'] }}</strong> - {{ $error['observacion'] }}</div>
            @empty
                <div class="px-5 py-8 text-sm text-slate-500">Sin errores.</div>
            @endforelse
        </div>
        @if (auth()->user()->role->canModifyCi())
            <form method="POST" action="{{ route('actividades.asistencias.revert', $activity) }}" class="border-t border-slate-200 bg-slate-50 px-5 py-4" onsubmit="return confirm('Se revertira este lote sin borrar registros.');">
                @csrf
                <input type="hidden" name="batch_id" value="{{ $summary['batch_id'] }}">
                <button class="btn-secondary">Revertir esta importacion</button>
            </form>
        @endif
    </div>
</x-app-layout>
