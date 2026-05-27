<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Nuevo registro</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Crear sindicato</h1>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('sindicatos.store') }}" class="panel overflow-hidden">
        @csrf
        @include('sindicatos.partials.form', ['button' => 'Guardar sindicato'])
    </form>
</x-app-layout>
