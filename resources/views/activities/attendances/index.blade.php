<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Asistencia</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $activity->nombre }}</h1>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('actividades.show', $activity) }}" class="btn-secondary">Volver</a>
                <a href="{{ route('actividades.asistencias.report', $activity) }}" class="btn-secondary">Reporte</a>
                <a href="{{ route('actividades.asistencias.export', ['activity' => $activity, 'sindicato_id' => request('sindicato_id')]) }}" class="btn-secondary">Exportar CSV</a>
                @if (auth()->user()->role->canManageAffiliates())
                    <a href="{{ route('actividades.asistencias.import.form', $activity) }}" class="btn-primary">Importar CSV</a>
                @endif
            </div>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('actividades.asistencias.index', $activity) }}" class="panel mb-6 p-4">
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
            <a href="{{ route('actividades.asistencias.index', $activity) }}" class="btn-ghost">Limpiar</a>
        </div>
    </form>

    <section class="mb-6 grid gap-4 sm:grid-cols-4">
        @foreach (['validos' => 'Validos', 'duplicados' => 'Duplicados', 'observados' => 'Observados', 'invalidos' => 'Invalidos'] as $key => $label)
            <div class="panel p-5">
                <div class="text-sm font-semibold text-slate-500">{{ $label }}</div>
                <div class="mt-2 text-3xl font-bold text-slate-950">{{ $summary[$key] }}</div>
            </div>
        @endforeach
    </section>

    @if ($batches->isNotEmpty())
        <section class="panel mb-6 overflow-hidden">
            <div class="border-b border-slate-200 px-5 py-4">
                <h2 class="text-base font-bold text-slate-950">Lotes importados</h2>
                <p class="mt-1 text-sm text-slate-500">Cada lote puede revertirse sin borrar los registros historicos.</p>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach ($batches as $batch)
                    <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">{{ $batch->source_file_name ?? 'CSV importado' }}</p>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $batch->total_rows }} filas registradas · {{ optional($batch->imported_at)->format('d/m/Y H:i') }}
                            </p>
                            <p class="mt-1 break-all text-[11px] text-slate-400">{{ $batch->import_batch_id }}</p>
                        </div>
                        @if (auth()->user()->role->canModifyCi())
                            <form method="POST" action="{{ route('actividades.asistencias.revert', $activity) }}" onsubmit="return confirm('Esta accion revertira el lote seleccionado.');">
                                @csrf
                                <input type="hidden" name="batch_id" value="{{ $batch->import_batch_id }}">
                                <button class="btn-secondary text-red-700" type="submit">Revertir lote</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3">C.I.</th>
                        <th class="px-5 py-3">Afiliado</th>
                        <th class="px-5 py-3">Sindicato</th>
                        <th class="px-5 py-3">Observacion</th>
                        <th class="px-5 py-3">Lote</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($attendances as $attendance)
                        <tr>
                            <td class="px-5 py-4"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold capitalize">{{ $attendance->estado }}</span></td>
                            <td class="px-5 py-4 font-semibold">{{ $attendance->ci_detectado }}</td>
                            <td class="px-5 py-4">{{ $attendance->affiliate?->full_name ?? 'No vinculado' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $attendance->affiliate?->sindicato?->sigla ?? $attendance->affiliate?->sindicato?->nombre ?? 'Sin sindicato' }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $attendance->observacion }}</td>
                            <td class="px-5 py-4 text-xs text-slate-500">{{ $attendance->import_batch_id }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-10 text-center text-slate-500">Sin asistencias importadas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $attendances->links() }}</div>
    </div>
</x-app-layout>
