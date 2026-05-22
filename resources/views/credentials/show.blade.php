<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Credencial digital</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $affiliate->full_name }}</h1>
                <p class="mt-1 text-sm text-slate-600">QR generado con C.I. {{ $credential->qr_payload }}</p>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row">
                <a href="{{ route('afiliados.show', $affiliate) }}" class="btn-secondary">Volver</a>
                <button type="button" class="btn-primary" data-download-credential-png data-filename="credencial-{{ $affiliate->ci }}-canva.png" data-audit-url="{{ route('afiliados.credential.png.audit', $affiliate) }}" data-auto-download="{{ request()->boolean('descargar_png') ? 'true' : 'false' }}">
                    Descargar PNG Canva
                </button>
                <a href="{{ route('afiliados.credential.print', $affiliate) }}" class="btn-secondary" target="_blank">Imprimir credencial</a>
                @if (auth()->user()->role->canModifyCi())
                    <form method="POST" action="{{ route('afiliados.credential.regenerate', $affiliate) }}">
                        @csrf
                        <button type="submit" class="btn-secondary w-full">Regenerar QR</button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="grid gap-6 xl:grid-cols-[1fr_340px]">
        <section class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Vista previa</h2>
            </div>
            <div class="overflow-x-auto p-5">
                <div class="credential-preview" data-credential-export-area>
                    @include('credentials.partials.card')
                </div>
            </div>
        </section>

        <aside class="panel h-fit overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Datos QR</h2>
            </div>
            <div class="space-y-4 p-5 text-sm">
                <div>
                    <div class="font-medium text-slate-500">Payload</div>
                    <div class="mt-1 font-semibold text-slate-950">{{ $credential->qr_payload }}</div>
                </div>
                <div>
                    <div class="font-medium text-slate-500">Version</div>
                    <div class="mt-1 font-semibold text-slate-950">{{ $credential->qr_version }}</div>
                </div>
                <div>
                    <div class="font-medium text-slate-500">Ultima regeneracion</div>
                    <div class="mt-1 font-semibold text-slate-950">{{ $credential->regenerated_at?->format('d/m/Y H:i') ?? 'Pendiente' }}</div>
                </div>
            </div>
        </aside>
    </div>
</x-app-layout>
