<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Panel principal</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Dashboard</h1>
                <p class="mt-1 text-sm text-slate-600">Resumen operativo de afiliados y movimientos recientes.</p>
            </div>
            <a href="{{ route('afiliados.index') }}" class="btn-primary">Ver afiliados</a>
        </div>
    </x-slot>

    <section class="mb-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="panel p-5">
            <div class="text-sm font-semibold text-slate-500">Actividades realizadas</div>
            <div class="mt-2 text-3xl font-bold text-slate-950">{{ $activitiesDone }}</div>
        </div>
        <div class="panel p-5">
            <div class="text-sm font-semibold text-slate-500">Ultima actividad</div>
            <div class="mt-2 text-lg font-bold text-slate-950">{{ $latestActivity?->nombre ?? 'Sin actividad' }}</div>
            <div class="mt-1 text-sm text-slate-500">{{ $latestActivity?->fecha?->format('d/m/Y') }}</div>
        </div>
        <div class="panel p-5">
            <div class="text-sm font-semibold text-slate-500">Promedio asistencia</div>
            <div class="mt-2 text-3xl font-bold text-slate-950">{{ $averageAttendance }}%</div>
        </div>
        <div class="panel p-5">
            <div class="text-sm font-semibold text-slate-500">Baja asistencia</div>
            <div class="mt-2 text-3xl font-bold text-slate-950">{{ $lowAttendanceCount }}</div>
            <div class="mt-1 text-sm text-slate-500">Afiliados bajo 50%</div>
        </div>
    </section>

    <section class="grid gap-4 xl:grid-cols-[1.4fr_1fr]">
        <div class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Resumen general</h2>
            </div>
            <div class="grid gap-4 p-5 sm:grid-cols-2">
                <div class="rounded-lg border border-cyan-100 bg-cyan-50 p-5">
                    <div class="text-sm font-semibold text-cyan-800">Total afiliados</div>
                    <div class="mt-3 text-4xl font-bold text-slate-950">{{ $totalAffiliates }}</div>
                    <div class="mt-2 text-sm text-slate-600">Registros cargados en el sistema</div>
                </div>

                <div class="grid gap-3">
                    @foreach ($statusCounts as $status => $count)
                        <div class="flex items-center justify-between rounded-md border border-slate-200 bg-white px-4 py-3">
                            <div class="flex items-center gap-3">
                                <span class="h-2.5 w-2.5 rounded-full {{ $status === 'activo' ? 'bg-emerald-500' : ($status === 'baja' ? 'bg-rose-500' : ($status === 'suspendido' ? 'bg-amber-500' : 'bg-sky-500')) }}"></span>
                                <span class="text-sm font-semibold capitalize text-slate-700">{{ $status }}</span>
                            </div>
                            <span class="text-lg font-bold text-slate-950">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Accesos rapidos</h2>
            </div>
            <div class="space-y-3 p-5">
                    <a href="{{ route('afiliados.index') }}" class="flex items-center justify-between rounded-md border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-900">
                        Consultar afiliados
                        <span class="text-cyan-800">&rarr;</span>
                    </a>
                @if (auth()->user()->role->canManageAffiliates())
                    <a href="{{ route('afiliados.create') }}" class="flex items-center justify-between rounded-md border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-900">
                        Registrar afiliado
                        <span class="text-cyan-800">&rarr;</span>
                    </a>
                @endif
                <a href="{{ route('actividades.index') }}" class="flex items-center justify-between rounded-md border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-cyan-200 hover:bg-cyan-50 hover:text-cyan-900">
                    Gestionar actividades
                    <span class="text-cyan-800">&rarr;</span>
                </a>
            </div>
        </div>
    </section>

    <section class="panel mt-6 overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-4">
            <h2 class="section-title">Auditoria reciente</h2>
            <span class="text-xs font-medium text-slate-500">Ultimos 5 eventos</span>
        </div>
        <div class="divide-y divide-slate-100">
            @forelse ($latestAudits as $audit)
                <div class="flex flex-col gap-1 px-5 py-4 text-sm sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="font-semibold text-slate-900">{{ $audit->action }}</div>
                        <div class="text-slate-500">{{ $audit->user?->name ?? 'Sistema' }}</div>
                    </div>
                    <time class="text-xs font-medium text-slate-500">{{ $audit->created_at->format('d/m/Y H:i') }}</time>
                </div>
            @empty
                <div class="px-5 py-8 text-center text-sm text-slate-500">Todavia no hay eventos registrados.</div>
            @endforelse
        </div>
    </section>
</x-app-layout>
