<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Ficha de afiliado</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $affiliate->full_name }}</h1>
                <p class="mt-1 text-sm text-slate-600">C.I. {{ $affiliate->ci }}</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('afiliados.index') }}" class="btn-secondary">Volver</a>
                <a href="{{ route('afiliados.credential.show', $affiliate) }}" class="btn-secondary">Ver credencial</a>
                <a href="{{ route('afiliados.credential.show', ['affiliate' => $affiliate, 'descargar_png' => 1]) }}" class="btn-secondary">Descargar PNG</a>
                <a href="{{ route('afiliados.credential.print', $affiliate) }}" class="btn-secondary" target="_blank">Imprimir credencial</a>
                @if (auth()->user()->role->canManageAffiliates())
                    <a href="{{ route('afiliados.edit', $affiliate) }}" class="btn-primary">Editar</a>
                @endif
            </div>
        </div>
    </x-slot>

    @php
        $academicRows = collect($affiliate->formacion_academica ?? [])->filter();
        $languages = collect([
            'Castellano' => $affiliate->idioma_castellano,
            'Ingles' => $affiliate->idioma_ingles,
            'Aymara' => $affiliate->idioma_aymara,
            'Quechua' => $affiliate->idioma_quechua,
        ])->filter()->keys();
    @endphp

    <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
        <div class="space-y-6">
            <section class="panel overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h2 class="section-title">Datos personales</h2>
                </div>
                <dl class="grid gap-0 divide-y divide-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Nombres</dt>
                        <dd class="mt-1 font-semibold text-slate-950">{{ $affiliate->nombres ?? $affiliate->first_name }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Apellidos</dt>
                        <dd class="mt-1 font-semibold text-slate-950">{{ trim(($affiliate->apellido_paterno ?? '').' '.($affiliate->apellido_materno ?? '')) ?: $affiliate->last_name }}</dd>
                    </div>
                </dl>
                <dl class="grid gap-0 divide-y divide-slate-100 border-t border-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Lugar y fecha de nacimiento</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->lugar_fecha_nacimiento ?? 'Sin dato' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Nacionalidad</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->nacionalidad ?? 'Sin dato' }}</dd>
                    </div>
                </dl>
                <dl class="grid gap-0 divide-y divide-slate-100 border-t border-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Celular</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->celular ?? $affiliate->phone ?? 'Sin dato' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Telefono / email</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->telefono ?? 'Sin telefono' }} - {{ $affiliate->email ?? 'Sin email' }}</dd>
                    </div>
                </dl>
                <dl class="divide-y divide-slate-100 border-t border-slate-100">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Domicilio</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->domicilio ?? $affiliate->address ?? 'Sin dato' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Idiomas</dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            @forelse ($languages as $language)
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $language }}</span>
                            @empty
                                <span class="text-sm text-slate-500">Sin idiomas registrados</span>
                            @endforelse
                            @if ($affiliate->idioma_otros)
                                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700">{{ $affiliate->idioma_otros }}</span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            <section class="panel overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h2 class="section-title">Formacion academica</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($academicRows as $row)
                        <div class="grid gap-4 p-5 md:grid-cols-2">
                            <div>
                                <div class="text-sm font-medium text-slate-500">Carrera</div>
                                <div class="mt-1 font-semibold text-slate-950">{{ $row['carrera'] ?? 'Sin dato' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-500">Universidad</div>
                                <div class="mt-1 text-slate-950">{{ $row['universidad'] ?? 'Sin dato' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-500">Titulo academico</div>
                                <div class="mt-1 text-slate-950">{{ $row['titulo_academico_numero'] ?? 'Sin numero' }} - {{ $row['titulo_academico_fecha'] ?? 'Sin fecha' }}</div>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-slate-500">Titulo provision nacional</div>
                                <div class="mt-1 text-slate-950">{{ $row['titulo_provision_nacional_numero'] ?? 'Sin numero' }} - {{ $row['titulo_provision_nacional_fecha'] ?? 'Sin fecha' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-8 text-sm text-slate-500">Sin formacion academica registrada.</div>
                    @endforelse
                </div>
            </section>

            <section class="panel overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h2 class="section-title">Formacion postgrado</h2>
                </div>
                <div class="grid gap-0 divide-y divide-slate-100 md:grid-cols-3 md:divide-x md:divide-y-0">
                    @foreach (['diplomado' => 'Diplomado', 'especialidad' => 'Especialidad', 'maestria' => 'Maestria'] as $prefix => $label)
                        <div class="p-5">
                            <h3 class="font-bold text-slate-800">{{ $label }}</h3>
                            <dl class="mt-3 space-y-3 text-sm">
                                <div>
                                    <dt class="text-slate-500">Universidad</dt>
                                    <dd class="font-medium text-slate-900">{{ $affiliate->{$prefix.'_universidad'} ?? 'Sin dato' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Anio</dt>
                                    <dd class="font-medium text-slate-900">{{ $affiliate->{$prefix.'_anio'} ?? 'Sin dato' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-slate-500">Titulo</dt>
                                    <dd class="font-medium text-slate-900">{{ $affiliate->{$prefix.'_titulo'} ?? 'Sin dato' }}</dd>
                                </div>
                            </dl>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="panel overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h2 class="section-title">Informacion laboral</h2>
                </div>
                <dl class="grid gap-0 divide-y divide-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Lugar de trabajo</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->lugar_trabajo ?? 'Sin dato' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Red de salud</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->red_salud ?? 'Sin dato' }}</dd>
                    </div>
                </dl>
                <dl class="grid gap-0 divide-y divide-slate-100 border-t border-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Item principal</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->item_principal ?? 'Sin dato' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Item secundario / tipo</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->item_secundario ?? 'Sin item secundario' }} - {{ $affiliate->tipo_item ?? 'Sin tipo' }}</dd>
                    </div>
                </dl>
                <dl class="border-t border-slate-100">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Sindicato</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->sindicato?->nombre ?? 'Sin sindicato asignado' }}</dd>
                    </div>
                </dl>
                <dl class="grid gap-0 divide-y divide-slate-100 border-t border-slate-100 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Ingreso al sistema</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->fecha_ingreso_sistema?->format('d/m/Y') ?? $affiliate->joined_at?->format('d/m/Y') ?? 'Sin dato' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Primer descuento FESIRMES</dt>
                        <dd class="mt-1 text-slate-950">{{ $affiliate->fecha_primer_descuento_fesirmes?->format('d/m/Y') ?? 'Sin dato' }}</dd>
                    </div>
                </dl>
            </section>

            <section class="panel overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h2 class="section-title">Informacion adicional</h2>
                </div>
                <dl class="divide-y divide-slate-100">
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Tematica de capacitacion</dt>
                        <dd class="mt-1 whitespace-pre-line text-slate-950">{{ $affiliate->tematica_capacitacion ?? 'Sin dato' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Deportes</dt>
                        <dd class="mt-1 whitespace-pre-line text-slate-950">{{ $affiliate->deportes ?? 'Sin dato' }}</dd>
                    </div>
                    <div class="p-5">
                        <dt class="text-sm font-medium text-slate-500">Notas internas</dt>
                        <dd class="mt-1 whitespace-pre-line text-slate-950">{{ $affiliate->notes ?? 'Sin notas' }}</dd>
                    </div>
                </dl>
            </section>
        </div>

        <aside class="space-y-6">
            <section class="panel overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h2 class="section-title">Fotografia</h2>
                </div>
                <div class="flex flex-col items-center p-5 text-center">
                    @include('affiliates.partials.photo-avatar', ['affiliate' => $affiliate, 'size' => 'h-40 w-40', 'text' => 'text-4xl'])
                    <div class="mt-4 text-sm font-semibold text-slate-950">{{ $affiliate->full_name }}</div>
                    <div class="mt-1 text-xs text-slate-500">C.I. {{ $affiliate->ci }}</div>
                </div>
            </section>

            <section class="panel overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h2 class="section-title">Estado</h2>
                </div>
                <div class="p-5">
                    <div class="text-sm font-medium text-slate-500">Estado actual</div>
                    <div class="mt-3">
                        <span class="status-badge status-{{ $affiliate->status->value }}">{{ $affiliate->status->value }}</span>
                    </div>
                    <div class="mt-5 rounded-md bg-slate-50 p-3 text-sm text-slate-600">
                        La baja conserva el registro historico y solo cambia el estado del afiliado.
                    </div>
                    @if (auth()->user()->role->canModifyCi() && $affiliate->status->value !== 'baja')
                        <form method="POST" action="{{ route('afiliados.destroy', $affiliate) }}" class="mt-6" onsubmit="return confirm('Esto marcara al afiliado como baja. No se eliminara fisicamente.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn-secondary w-full" type="submit">Marcar como baja</button>
                        </form>
                    @endif
                </div>
            </section>

            @if (auth()->user()->role->canModifyCi())
                <section class="panel overflow-hidden">
                    <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                        <h2 class="section-title">Usuario afiliado</h2>
                    </div>
                    <div class="space-y-3 p-5 text-sm">
                        <div>
                            <div class="text-slate-500">Usuario</div>
                            <div class="font-semibold text-slate-950">{{ $affiliate->user?->email ?? 'Sin usuario generado' }}</div>
                        </div>
                        @if ($affiliate->user)
                            <div>
                                <div class="text-slate-500">Estado usuario</div>
                                <div class="font-semibold text-slate-950">{{ $affiliate->user->is_blocked ? 'Bloqueado' : 'Activo' }}</div>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('afiliados.users.reset', $affiliate) }}">
                            @csrf
                            <button class="btn-secondary w-full" type="submit">Resetear contraseña</button>
                        </form>
                        @if ($affiliate->user?->is_blocked)
                            <form method="POST" action="{{ route('afiliados.users.unblock', $affiliate) }}">
                                @csrf
                                <button class="btn-primary w-full" type="submit">Activar usuario</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('afiliados.users.block', $affiliate) }}">
                                @csrf
                                <button class="btn-secondary w-full" type="submit">Bloquear usuario</button>
                            </form>
                        @endif
                    </div>
                </section>
            @endif
        </aside>
    </div>
</x-app-layout>
