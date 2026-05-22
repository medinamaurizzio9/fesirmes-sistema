<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Afiliados</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Nuevo afiliado</h1>
            <p class="mt-1 text-sm text-slate-600">Completa los datos basicos para registrar a una persona afiliada.</p>
        </div>
    </x-slot>

    <div class="panel overflow-hidden">
        <form method="POST" action="{{ route('afiliados.store') }}" enctype="multipart/form-data">
            @csrf
            @include('affiliates.partials.form', ['button' => 'Guardar afiliado'])
        </form>
    </div>
</x-app-layout>
