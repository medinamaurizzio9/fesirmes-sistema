<div class="grid gap-5 p-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="input-label" for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="{{ old('nombre', $activity->nombre) }}" class="input-field" required>
        @error('nombre')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="md:col-span-2">
        <label class="input-label" for="descripcion">Descripcion</label>
        <textarea id="descripcion" name="descripcion" rows="3" class="input-field">{{ old('descripcion', $activity->descripcion) }}</textarea>
    </div>
    <div>
        <label class="input-label" for="lugar">Lugar</label>
        <input id="lugar" name="lugar" value="{{ old('lugar', $activity->lugar) }}" class="input-field">
    </div>
    <div>
        <label class="input-label" for="fecha">Fecha</label>
        <input id="fecha" name="fecha" type="date" value="{{ old('fecha', $activity->fecha?->format('Y-m-d')) }}" class="input-field" required>
        @error('fecha')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="input-label" for="hora_inicio">Hora inicio</label>
        <input id="hora_inicio" name="hora_inicio" type="time" value="{{ old('hora_inicio', $activity->hora_inicio ? substr($activity->hora_inicio, 0, 5) : null) }}" class="input-field" required>
    </div>
    <div>
        <label class="input-label" for="hora_fin">Hora fin</label>
        <input id="hora_fin" name="hora_fin" type="time" value="{{ old('hora_fin', $activity->hora_fin ? substr($activity->hora_fin, 0, 5) : null) }}" class="input-field">
    </div>
    <div>
        <label class="input-label" for="estado">Estado</label>
        <select id="estado" name="estado" class="input-field" required>
            @foreach (['programada', 'realizada', 'cancelada'] as $estado)
                <option value="{{ $estado }}" @selected(old('estado', $activity->estado ?? 'programada') === $estado)>{{ ucfirst($estado) }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="input-label" for="observaciones">Observaciones</label>
        <textarea id="observaciones" name="observaciones" rows="3" class="input-field">{{ old('observaciones', $activity->observaciones) }}</textarea>
    </div>
</div>
<div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end">
    <a href="{{ route('actividades.index') }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">{{ $button }}</button>
</div>
