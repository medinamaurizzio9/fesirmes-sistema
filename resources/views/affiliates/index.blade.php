<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Registro institucional</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Afiliados</h1>
                <p class="mt-1 text-sm text-slate-600">Busqueda, consulta y administracion basica.</p>
            </div>
            @if (auth()->user()->role->canManageAffiliates())
                <div class="flex flex-wrap gap-2">
                    @if (auth()->user()->role->canModifyCi())
                        <form method="POST" action="{{ route('afiliados.users.generate') }}">
                            @csrf
                            <button class="btn-secondary" type="submit">Generar usuarios</button>
                        </form>
                    @endif
                    <a href="{{ route('afiliados.create') }}" class="btn-primary">Nuevo afiliado</a>
                </div>
            @endif
        </div>
    </x-slot>

    <form method="GET" action="{{ route('afiliados.index') }}" class="panel mb-6 p-4">
        <div class="grid gap-4 lg:grid-cols-[1fr_170px_170px_220px_auto_auto] lg:items-end">
            <div>
                <label class="input-label" for="buscar">Buscar</label>
                <div class="relative">
                    <input id="buscar" name="buscar" value="{{ request('buscar') }}" class="input-field pl-10" placeholder="C.I., nombre o apellido">
                    <svg class="pointer-events-none absolute left-3 top-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15Z" />
                    </svg>
                </div>
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
                <label class="input-label" for="tipo_item">Tipo de item</label>
                <select id="tipo_item" name="tipo_item" class="input-field">
                    <option value="">Todos</option>
                    @foreach ($itemTypes as $itemType)
                        <option value="{{ $itemType }}" @selected(request('tipo_item') === $itemType)>{{ $itemType }}</option>
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
            <button class="btn-primary w-full" type="submit">Filtrar</button>
            <a href="{{ route('afiliados.index') }}" class="btn-ghost w-full">Limpiar</a>
        </div>
    </form>

    <div class="panel overflow-hidden">
        <div class="flex flex-col gap-2 border-b border-slate-200 bg-white px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="section-title">Listado</h2>
                <p class="mt-1 text-sm text-slate-500">Resultados paginados con filtros activos.</p>
            </div>
            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $affiliates->total() }} registros</span>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Afiliado</th>
                        <th class="px-5 py-3">C.I.</th>
                        <th class="px-5 py-3">Nombre</th>
                        <th class="px-5 py-3">Sindicato</th>
                        <th class="px-5 py-3">Estado</th>
                        <th class="px-5 py-3">Contacto / item</th>
                        <th class="px-5 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse ($affiliates as $affiliate)
                        <tr class="transition hover:bg-slate-50">
                            <td class="px-5 py-4">
                                @include('affiliates.partials.photo-avatar', ['affiliate' => $affiliate])
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-slate-900">{{ $affiliate->ci }}</td>
                            <td class="px-5 py-4">
                                <div class="font-semibold text-slate-900">{{ $affiliate->full_name }}</div>
                                <div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                    <span>{{ $affiliate->lugar_trabajo ?? 'Afiliado #'.$affiliate->id }}</span>
                                    @if ($affiliate->is_directorio)
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">Directorio</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $affiliate->sindicato?->sigla ?? $affiliate->sindicato?->nombre ?? 'Sin sindicato' }}</td>
                            <td class="px-5 py-4">
                                <span class="status-badge status-{{ $affiliate->status->value }}">{{ $affiliate->status->value }}</span>
                            </td>
                            <td class="px-5 py-4 text-slate-600">
                                <div>{{ $affiliate->celular ?? $affiliate->phone ?? $affiliate->email ?? 'Sin contacto' }}</div>
                                @if ($affiliate->item_principal)
                                    <div class="mt-1 text-xs text-slate-500">Item {{ $affiliate->item_principal }}</div>
                                @endif
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('afiliados.show', $affiliate) }}" class="btn-secondary">Ver</a>
                                    @if (auth()->user()->role->canManageAffiliates())
                                        <a href="{{ route('afiliados.edit', $affiliate) }}" class="btn-secondary">Editar</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-500">No hay afiliados con esos filtros.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-slate-100 md:hidden">
            @forelse ($affiliates as $affiliate)
                <div class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 gap-3">
                            @include('affiliates.partials.photo-avatar', ['affiliate' => $affiliate])
                            <div class="min-w-0">
                                <div class="truncate font-semibold text-slate-950">{{ $affiliate->full_name }}</div>
                                <div class="mt-1 text-sm text-slate-500">C.I. {{ $affiliate->ci }}</div>
                                @if ($affiliate->is_directorio)
                                    <div class="mt-2">
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-emerald-700 ring-1 ring-inset ring-emerald-200">Directorio</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <span class="status-badge status-{{ $affiliate->status->value }}">{{ $affiliate->status->value }}</span>
                    </div>
                    <div class="mt-3 text-sm text-slate-600">{{ $affiliate->celular ?? $affiliate->phone ?? $affiliate->email ?? 'Sin contacto' }}</div>
                    <div class="mt-1 text-xs text-slate-500">Sindicato: {{ $affiliate->sindicato?->sigla ?? $affiliate->sindicato?->nombre ?? 'Sin sindicato' }}</div>
                    @if ($affiliate->item_principal)
                        <div class="mt-1 text-xs text-slate-500">Item principal: {{ $affiliate->item_principal }}</div>
                    @endif
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('afiliados.show', $affiliate) }}" class="btn-secondary flex-1">Ver</a>
                        @if (auth()->user()->role->canManageAffiliates())
                            <a href="{{ route('afiliados.edit', $affiliate) }}" class="btn-secondary flex-1">Editar</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center text-sm text-slate-500">No hay afiliados con esos filtros.</div>
            @endforelse
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">
            {{ $affiliates->links() }}
        </div>
    </div>
</x-app-layout>
