<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Calidad de datos</h1>
            </div>
            <a href="{{ route('reportes.index') }}" class="btn-secondary">Volver</a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('reportes.quality') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 lg:grid-cols-4">
            <div>
                <label class="input-label" for="categoria">Tipo de problema</label>
                <select id="categoria" name="categoria" class="input-field">
                    @foreach ($categories as $key => $category)
                        <option value="{{ $key }}" @selected($selected === $key)>{{ $category['label'] }}</option>
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
        </div>
        <div class="mt-4 flex flex-wrap gap-2">
            <button class="btn-primary" type="submit">Filtrar</button>
            <a href="{{ route('reportes.quality') }}" class="btn-ghost">Limpiar</a>
            @if ($canExport)
                <a href="{{ route('reportes.quality.csv', request()->query()) }}" class="btn-secondary">Exportar CSV</a>
                <a href="{{ route('reportes.quality.pdf', request()->query()) }}" class="btn-secondary">Exportar PDF</a>
            @endif
        </div>
    </form>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($categories as $key => $category)
            <a href="{{ route('reportes.quality', array_merge(request()->except('page'), ['categoria' => $key])) }}" class="panel p-5 {{ $selected === $key ? 'ring-2 ring-cyan-700' : '' }}">
                <div class="text-sm font-semibold text-slate-500">{{ $category['label'] }}</div>
                <div class="mt-2 text-3xl font-bold text-slate-950">{{ $counts[$key] }}</div>
            </a>
        @endforeach
    </section>

    <div class="panel overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
            <h2 class="section-title">{{ $categories[$selected]['label'] }}</h2>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($affiliates as $affiliate)
                <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="font-semibold text-slate-950">{{ $affiliate->full_name }}</div>
                        <div class="mt-1 text-sm text-slate-500">C.I. {{ $affiliate->ci }} · {{ $affiliate->sindicato?->sigla ?? $affiliate->sindicato?->nombre ?? 'Sin sindicato' }}</div>
                    </div>
                    <a href="{{ auth()->user()->role->canManageAffiliates() ? route('afiliados.edit', $affiliate) : route('afiliados.show', $affiliate) }}" class="btn-secondary">
                        {{ auth()->user()->role->canManageAffiliates() ? 'Corregir' : 'Ver' }}
                    </a>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-slate-500">Sin registros en esta categoria.</div>
            @endforelse
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $affiliates->links() }}</div>
    </div>
</x-app-layout>
