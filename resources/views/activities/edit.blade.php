<x-app-layout>
    <x-slot name="header"><h1 class="text-2xl font-bold text-slate-950">Editar actividad</h1></x-slot>
    <div class="panel overflow-hidden">
        <form method="POST" action="{{ route('actividades.update', $activity) }}">
            @csrf
            @method('PUT')
            @include('activities.partials.form', ['button' => 'Actualizar actividad'])
        </form>
    </div>
</x-app-layout>
