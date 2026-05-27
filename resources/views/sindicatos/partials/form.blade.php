<div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
    <h2 class="section-title">Datos del sindicato</h2>
</div>

<div class="grid gap-5 p-5 md:grid-cols-2">
    <div class="md:col-span-2">
        <label class="input-label" for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="{{ old('nombre', $sindicato->nombre) }}" class="input-field" required>
        @error('nombre')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="input-label" for="sigla">Sigla</label>
        <input id="sigla" name="sigla" value="{{ old('sigla', $sindicato->sigla) }}" class="input-field">
        @error('sigla')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="input-label" for="estado">Estado</label>
        <select id="estado" name="estado" class="input-field" required>
            @foreach ($statuses as $status)
                <option value="{{ $status }}" @selected(old('estado', $sindicato->estado ?? 'activo') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        @error('estado')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>

    <div class="md:col-span-2">
        <label class="input-label" for="observaciones">Observaciones</label>
        <textarea id="observaciones" name="observaciones" rows="4" class="input-field">{{ old('observaciones', $sindicato->observaciones) }}</textarea>
        @error('observaciones')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:justify-end">
    <a href="{{ route('sindicatos.index') }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">{{ $button }}</button>
</div>
