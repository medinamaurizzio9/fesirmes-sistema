<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Reporte por sindicato</h1>
            </div>
            <a href="{{ route('reportes.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('reportes.sindicatos') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 lg:grid-cols-4">
            <div>
                <label class="input-label" for="estado_sindicato">Estado sindicato</label>
                <select id="estado_sindicato" name="estado_sindicato" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($sindicatoStatuses as $status)
                        <option value="{{ $status }}" @selected(request('estado_sindicato') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="input-label" for="sindicato_id">Sindicato</label>
                <select id="sindicato_id" name="sindicato_id" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($sindicatoOptions as $option)
                        <option value="{{ $option->id }}" @selected((int) request('sindicato_id') === $option->id)>{{ $option->sigla ?? $option->nombre }}</option>
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
                <label class="input-label" for="estado_afiliado">Estado afiliado</label>
                <select id="estado_afiliado" name="estado_afiliado" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('estado_afiliado') === $status->value)>{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button class="btn-primary" type="submit">Filtrar</button>
            <a href="{{ route('reportes.sindicatos') }}" class="btn-ghost">Limpiar</a>
            @if ($canExport)
                <a href="{{ route('reportes.sindicatos.csv', request()->query()) }}" class="btn-secondary">Exportar CSV</a>
                <a href="{{ route('reportes.sindicatos.pdf', request()->query()) }}" class="btn-secondary">Exportar PDF</a>
            @endif
        </div>
    </form>

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
                        <th class="px-5 py-3">Porcentaje</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($sindicatos as $sindicato)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $sindicato->nombre }}</td>
                            <td class="px-5 py-4">{{ $sindicato->sigla ?? 'Sin sigla' }}</td>
                            <td class="px-5 py-4">{{ $sindicato->total_afiliados }}</td>
                            <td class="px-5 py-4">{{ $sindicato->activos }}</td>
                            <td class="px-5 py-4">{{ $sindicato->bajas }}</td>
                            <td class="px-5 py-4">{{ $sindicato->suspendidos }}</td>
                            <td class="px-5 py-4">{{ $sindicato->observados }}</td>
                            <td class="px-5 py-4">{{ round(($sindicato->total_afiliados / $total) * 100, 2) }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-slate-500">Sin sindicatos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $sindicatos->links() }}</div>
    </div>
</x-app-layout>
