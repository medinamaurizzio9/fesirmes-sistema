<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Importar asistencia</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">{{ $activity->nombre }}</h1>
            <p class="mt-1 text-sm text-slate-600">CSV con C.I. escaneados desde QR. El QR sigue conteniendo solo el C.I.</p>
        </div>
    </x-slot>

    <div class="panel overflow-hidden">
        <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
            <h2 class="section-title">Archivo CSV</h2>
        </div>
        <form method="POST" action="{{ route('actividades.asistencias.import', $activity) }}" enctype="multipart/form-data" class="space-y-5 p-5">
            @csrf
            <div>
                <label class="input-label" for="csv_file">CSV</label>
                <input id="csv_file" name="csv_file" type="file" accept=".csv,text/csv" class="input-field file:mr-4 file:rounded-md file:border-0 file:bg-cyan-800 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white" required data-csv-file-input>
                @error('csv_file')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                <p class="mt-2 text-xs text-slate-500">Maximo 5MB. Si tiene varias columnas, escribe el nombre exacto de la columna del C.I.</p>
            </div>
            <div>
                <label class="input-label" for="ci_column">Columna de C.I.</label>
                <select id="ci_column" name="ci_column" class="input-field" data-ci-column-select>
                    <option value="">Detectar automaticamente</option>
                    <option value="ci">ci</option>
                </select>
                <p class="mt-2 text-xs text-slate-500">Al seleccionar el archivo se cargaran aqui las columnas detectadas.</p>
            </div>
            <div class="rounded-md bg-slate-50 p-4 text-sm text-slate-600">
                Ejemplo valido:<br>
                <code>ci<br>49951535<br>12345678</code>
            </div>
            <div class="flex justify-end gap-3 border-t border-slate-200 pt-5">
                <a href="{{ route('actividades.show', $activity) }}" class="btn-secondary">Cancelar</a>
                <button class="btn-primary">Importar</button>
            </div>
        </form>
    </div>
</x-app-layout>
