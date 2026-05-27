<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Edicion</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Editar sindicato</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('sindicatos.update', $sindicato) }}" class="panel overflow-hidden">
        @csrf
        @method('PUT')
        @include('sindicatos.partials.form', ['button' => 'Actualizar sindicato'])
    </form>
</x-app-layout>
