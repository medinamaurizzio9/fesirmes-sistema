<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Afiliados</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Editar afiliado</h1>
            <p class="mt-1 text-sm text-slate-600">{{ $affiliate->full_name }} - C.I. {{ $affiliate->ci }}</p>
        </div>
    </x-slot>

    <div class="panel overflow-hidden">
        <form method="POST" action="{{ route('afiliados.update', $affiliate) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('affiliates.partials.form', ['button' => 'Actualizar afiliado'])
        </form>
    </div>
</x-app-layout>
