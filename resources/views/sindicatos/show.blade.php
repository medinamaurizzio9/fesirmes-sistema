<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Detalle de sindicato</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $sindicato->nombre }}</h1>
                <p class="mt-1 text-sm text-slate-600">{{ $sindicato->sigla ?? 'Sin sigla' }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('sindicatos.index') }}" class="btn-secondary">Volver</a>
                @if (auth()->user()->role->canModifyCi())
                    <a href="{{ route('sindicatos.edit', $sindicato) }}" class="btn-primary">Editar</a>
                @endif
            </div>
        </div>
    </x-slot>

    <section class="mb-6 grid gap-4 sm:grid-cols-5">
        <div class="panel p-5">
            <div class="text-sm font-semibold text-slate-500">Total</div>
            <div class="mt-2 text-3xl font-bold text-slate-950">{{ $affiliates->total() }}</div>
        </div>
        @foreach ($statusCounts as $status => $count)
            <div class="panel p-5">
                <div class="text-sm font-semibold capitalize text-slate-500">{{ $status }}</div>
                <div class="mt-2 text-3xl font-bold text-slate-950">{{ $count }}</div>
            </div>
        @endforeach
    </section>

    <div class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <section class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Afiliados pertenecientes</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($affiliates as $affiliate)
                    <div class="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <div class="font-semibold text-slate-950">{{ $affiliate->full_name }}</div>
                            <div class="mt-1 text-sm text-slate-500">C.I. {{ $affiliate->ci }} · {{ $affiliate->item_principal ?? 'Sin item' }}</div>
                        </div>
                        <a href="{{ route('afiliados.show', $affiliate) }}" class="btn-secondary">Ver afiliado</a>
                    </div>
                @empty
                    <div class="px-5 py-8 text-sm text-slate-500">Sin afiliados asignados.</div>
                @endforelse
            </div>
            <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $affiliates->links() }}</div>
        </section>

        <aside class="panel h-fit overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Estado</h2>
            </div>
            <div class="p-5">
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold capitalize text-slate-700">{{ $sindicato->estado }}</span>
                <div class="mt-5 text-sm text-slate-500">Observaciones</div>
                <p class="mt-1 whitespace-pre-line text-sm text-slate-800">{{ $sindicato->observaciones ?? 'Sin observaciones' }}</p>

                @if (auth()->user()->role->canModifyCi())
                    @if ($sindicato->estado === 'activo')
                        <form method="POST" action="{{ route('sindicatos.destroy', $sindicato) }}" class="mt-6" onsubmit="return confirm('Se marcara el sindicato como inactivo.');">
                            @csrf
                            @method('DELETE')
                            <button class="btn-secondary w-full">Inactivar</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('sindicatos.activate', $sindicato) }}" class="mt-6">
                            @csrf
                            <button class="btn-primary w-full">Activar</button>
                        </form>
                    @endif
                @endif
            </div>
        </aside>
    </div>
</x-app-layout>
