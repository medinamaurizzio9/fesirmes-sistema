<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Catalogo institucional</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Sindicatos</h1>
                <p class="mt-1 text-sm text-slate-600">Sindicatos afiliados y afiliados directos de FESIRMES.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('sindicatos.report.general') }}" class="btn-secondary">Reporte</a>
                <a href="{{ route('sindicatos.report.attendance') }}" class="btn-secondary">Asistencia</a>
                @if (auth()->user()->role->canModifyCi())
                    <a href="{{ route('sindicatos.create') }}" class="btn-primary">Nuevo sindicato</a>
                @endif
            </div>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('sindicatos.index') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 lg:grid-cols-[1fr_200px_auto_auto] lg:items-end">
            <div>
                <label class="input-label" for="buscar">Buscar</label>
                <input id="buscar" name="buscar" value="{{ request('buscar') }}" class="input-field" placeholder="Nombre o sigla">
            </div>
            <div>
                <label class="input-label" for="estado">Estado</label>
                <select id="estado" name="estado" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}" @selected(request('estado') === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn-primary w-full" type="submit">Filtrar</button>
            <a href="{{ route('sindicatos.index') }}" class="btn-ghost w-full">Limpiar</a>
        </div>
    </form>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Sindicato</th>
                        <th class="px-5 py-3">Sigla</th>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3">Afiliados</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($sindicatos as $sindicato)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4 font-semibold text-slate-950">{{ $sindicato->nombre }}</td>
                            <td class="px-5 py-4 text-slate-600">{{ $sindicato->sigla ?? 'Sin sigla' }}</td>
                            <td class="px-5 py-4"><span class="rounded-full bg-slate-100 px-2 py-1 text-xs font-semibold capitalize text-slate-700">{{ $sindicato->estado }}</span></td>
                            <td class="px-5 py-4 font-semibold text-slate-900">{{ $sindicato->affiliates_count }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('sindicatos.show', $sindicato) }}" class="btn-secondary">Ver</a>
                                    @if (auth()->user()->role->canModifyCi())
                                        <a href="{{ route('sindicatos.edit', $sindicato) }}" class="btn-secondary">Editar</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No hay sindicatos con esos filtros.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $sindicatos->links() }}</div>
    </div>
</x-app-layout>
