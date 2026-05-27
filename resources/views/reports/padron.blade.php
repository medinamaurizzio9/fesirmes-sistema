<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Padron general</h1>
            </div>
            <a href="{{ route('reportes.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('reportes.padron') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 lg:grid-cols-3 xl:grid-cols-6">
            <div>
                <label class="input-label" for="buscar">Nombre o C.I.</label>
                <input id="buscar" name="buscar" value="{{ request('buscar') }}" class="input-field" placeholder="Buscar">
            </div>
            <div>
                <label class="input-label" for="estado">Estado</label>
                <select id="estado" name="estado" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('estado') === $status->value)>{{ $status->label() }}</option>
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
                <label class="input-label" for="sindicato_id">Sindicato</label>
                <select id="sindicato_id" name="sindicato_id" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($sindicatos as $sindicato)
                        <option value="{{ $sindicato->id }}" @selected((int) request('sindicato_id') === $sindicato->id)>{{ $sindicato->sigla ?? $sindicato->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="input-label" for="lugar_trabajo">Lugar trabajo</label>
                <input id="lugar_trabajo" name="lugar_trabajo" value="{{ request('lugar_trabajo') }}" class="input-field">
            </div>
            <div>
                <label class="input-label" for="red_salud">Red salud</label>
                <input id="red_salud" name="red_salud" value="{{ request('red_salud') }}" class="input-field">
            </div>
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button class="btn-primary" type="submit">Filtrar</button>
            <a href="{{ route('reportes.padron') }}" class="btn-ghost">Limpiar</a>
            @if ($canExport)
                <a href="{{ route('reportes.padron.csv', request()->query()) }}" class="btn-secondary">Exportar CSV</a>
            @endif
            @if ($canExport)
                <a href="{{ route('reportes.padron.pdf', request()->query()) }}" class="btn-secondary">Exportar PDF</a>
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
                        <th class="px-5 py-3">Tipo</th>
                        <th class="px-5 py-3">Sindicato</th>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3">Celular</th>
                        <th class="px-5 py-3">Trabajo / red</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($affiliates as $affiliate)
                        <tr>
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $affiliate->full_name }}</td>
                            <td class="px-5 py-4">{{ $affiliate->ci }}</td>
                            <td class="px-5 py-4">{{ $affiliate->item_principal ?? 'Sin item' }}</td>
                            <td class="px-5 py-4">{{ $affiliate->tipo_item ?? 'Sin tipo' }}</td>
                            <td class="px-5 py-4">{{ $affiliate->sindicato?->sigla ?? $affiliate->sindicato?->nombre ?? 'Sin sindicato' }}</td>
                            <td class="px-5 py-4">{{ $affiliate->status?->value }}</td>
                            <td class="px-5 py-4">{{ $affiliate->celular ?? $affiliate->phone ?? 'Sin celular' }}</td>
                            <td class="px-5 py-4">{{ $affiliate->lugar_trabajo ?? 'Sin trabajo' }} / {{ $affiliate->red_salud ?? 'Sin red' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-10 text-center text-slate-500">Sin resultados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $affiliates->links() }}</div>
    </div>
</x-app-layout>
