<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Reporte por tipo de item</h1>
            </div>
            <a href="{{ route('reportes.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('reportes.item-types') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 lg:grid-cols-3">
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
                <label class="input-label" for="estado">Estado</label>
                <select id="estado" name="estado" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}" @selected(request('estado') === $status->value)>{{ $status->label() }}</option>
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
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button class="btn-primary" type="submit">Filtrar</button>
            <a href="{{ route('reportes.item-types') }}" class="btn-ghost">Limpiar</a>
            @if ($canExport)
                <a href="{{ route('reportes.item-types.csv', request()->query()) }}" class="btn-secondary">Exportar CSV</a>
                <a href="{{ route('reportes.item-types.pdf', request()->query()) }}" class="btn-secondary">Exportar PDF</a>
            @endif
        </div>
    </form>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($counts as $type => $count)
            <div class="panel p-5">
                <div class="text-sm font-semibold text-slate-500">{{ $type }}</div>
                <div class="mt-2 text-3xl font-bold text-slate-950">{{ $count }}</div>
                <div class="mt-1 text-sm text-slate-500">{{ round(($count / $total) * 100, 2) }}%</div>
            </div>
        @endforeach
    </section>
</x-app-layout>
