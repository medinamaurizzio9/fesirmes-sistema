<x-app-layout>
    @php
        $portalAcademicRows = old('formacion_academica', $academicRows);
        $portalAcademicRows = array_pad(array_slice($portalAcademicRows, 0, 3), 3, []);
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Portal del afiliado</p>
                <h1 class="mt-1 text-2xl font-bold text-slate-950">Mi Perfil</h1>
                <p class="mt-1 text-sm text-slate-600">Actualiza tus datos personales, formacion y referencia institucional.</p>
            </div>
            @unless ($isRestricted)
                <div class="flex gap-2">
                    <a href="{{ route('affiliate.password.edit') }}" class="btn-secondary">Mi contraseña</a>
                    <a href="{{ route('affiliate.credential.show') }}" class="btn-secondary">Mi credencial</a>
                </div>
            @endunless
        </div>
    </x-slot>

    @if ($isRestricted)
        <section class="mb-6 rounded-lg border border-rose-200 bg-rose-50 p-5 text-rose-950 shadow-sm">
            <div class="text-xs font-bold uppercase tracking-wide text-rose-700">ESTADO: {{ $affiliate->portalStatusLabel() }}</div>
            <p class="mt-3 text-sm font-semibold">Su registro se encuentra restringido.</p>
            <p class="mt-2 max-w-3xl text-sm leading-6">
                Mientras mantenga este estado no podrá realizar modificaciones ni acceder a su credencial.
            </p>
            <p class="mt-2 max-w-3xl text-sm leading-6">
                Para regularizar su situación comuníquese con Administración o Secretaría de FESIRMES LA PAZ
            </p>
            <div class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <div class="rounded-md bg-white/70 p-3">
                    <div class="text-xs font-bold uppercase tracking-wide text-rose-700">Teléfono institucional</div>
                    <div class="mt-1 font-semibold">{{ $institution['institution_phones'] ?: 'No registrado' }}</div>
                </div>
                <div class="rounded-md bg-white/70 p-3">
                    <div class="text-xs font-bold uppercase tracking-wide text-rose-700">Correo institucional</div>
                    <div class="mt-1 font-semibold">{{ $institution['institution_email'] ?: 'No registrado' }}</div>
                </div>
            </div>
        </section>
    @endif

    <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
        <form method="POST" action="{{ route('affiliate.profile.update') }}" enctype="multipart/form-data" class="panel overflow-hidden">
            @csrf
            @method('PUT')
            <fieldset @disabled($isRestricted)>

            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">1. Datos personales</h2>
            </div>

            <div class="grid gap-5 p-5 md:grid-cols-2">
                <div>
                    <label class="input-label" for="nacionalidad">Nacionalidad</label>
                    <input id="nacionalidad" name="nacionalidad" value="{{ old('nacionalidad', $affiliate->nacionalidad) }}" class="input-field">
                    @error('nacionalidad')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label" for="lugar_fecha_nacimiento">Lugar y fecha de nacimiento</label>
                    <input id="lugar_fecha_nacimiento" name="lugar_fecha_nacimiento" value="{{ old('lugar_fecha_nacimiento', $affiliate->lugar_fecha_nacimiento) }}" class="input-field">
                    @error('lugar_fecha_nacimiento')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label" for="celular">Celular</label>
                    <input id="celular" name="celular" value="{{ old('celular', $affiliate->celular ?? $affiliate->phone) }}" class="input-field">
                    @error('celular')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label" for="telefono">Telefono</label>
                    <input id="telefono" name="telefono" value="{{ old('telefono', $affiliate->telefono) }}" class="input-field">
                    @error('telefono')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="input-label" for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $affiliate->email) }}" class="input-field">
                    @error('email')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="input-label" for="domicilio">Domicilio</label>
                    <input id="domicilio" name="domicilio" value="{{ old('domicilio', $affiliate->domicilio ?? $affiliate->address) }}" class="input-field">
                    @error('domicilio')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <span class="input-label">Idiomas</span>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ([
                            'idioma_castellano' => 'Castellano',
                            'idioma_ingles' => 'Ingles',
                            'idioma_aymara' => 'Aymara',
                            'idioma_quechua' => 'Quechua',
                        ] as $field => $label)
                            <label class="flex items-center gap-2 rounded-md border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700">
                                <input type="checkbox" name="{{ $field }}" value="1" class="rounded border-slate-300 text-cyan-800 focus:ring-cyan-700" @checked(old($field, $affiliate->{$field}))>
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                    <input name="idioma_otros" value="{{ old('idioma_otros', $affiliate->idioma_otros) }}" class="input-field mt-3" placeholder="Otros idiomas">
                    @error('idioma_otros')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="border-y border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">2. Formacion academica</h2>
            </div>

            <div class="space-y-5 p-5">
                @foreach ($portalAcademicRows as $index => $row)
                    <div class="rounded-lg border border-slate-200 p-4">
                        <h3 class="mb-4 text-sm font-bold text-slate-700">Registro academico {{ $index + 1 }}</h3>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="input-label" for="formacion_{{ $index }}_carrera">Carrera</label>
                                <input id="formacion_{{ $index }}_carrera" name="formacion_academica[{{ $index }}][carrera]" value="{{ $row['carrera'] ?? '' }}" class="input-field">
                            </div>
                            <div>
                                <label class="input-label" for="formacion_{{ $index }}_universidad">Universidad</label>
                                <input id="formacion_{{ $index }}_universidad" name="formacion_academica[{{ $index }}][universidad]" value="{{ $row['universidad'] ?? '' }}" class="input-field">
                            </div>
                            <div>
                                <label class="input-label" for="formacion_{{ $index }}_ta_numero">Titulo academico numero</label>
                                <input id="formacion_{{ $index }}_ta_numero" name="formacion_academica[{{ $index }}][titulo_academico_numero]" value="{{ $row['titulo_academico_numero'] ?? '' }}" class="input-field">
                            </div>
                            <div>
                                <label class="input-label" for="formacion_{{ $index }}_ta_fecha">Titulo academico fecha</label>
                                <input id="formacion_{{ $index }}_ta_fecha" name="formacion_academica[{{ $index }}][titulo_academico_fecha]" type="date" value="{{ $row['titulo_academico_fecha'] ?? '' }}" class="input-field">
                            </div>
                            <div>
                                <label class="input-label" for="formacion_{{ $index }}_tpn_numero">Titulo provision nacional numero</label>
                                <input id="formacion_{{ $index }}_tpn_numero" name="formacion_academica[{{ $index }}][titulo_provision_nacional_numero]" value="{{ $row['titulo_provision_nacional_numero'] ?? '' }}" class="input-field">
                            </div>
                            <div>
                                <label class="input-label" for="formacion_{{ $index }}_tpn_fecha">Titulo provision nacional fecha</label>
                                <input id="formacion_{{ $index }}_tpn_fecha" name="formacion_academica[{{ $index }}][titulo_provision_nacional_fecha]" type="date" value="{{ $row['titulo_provision_nacional_fecha'] ?? '' }}" class="input-field">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="border-y border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">3. Formacion postgrado</h2>
            </div>

            <div class="grid gap-5 p-5 md:grid-cols-3">
                @foreach (['diplomado' => 'Diplomado', 'especialidad' => 'Especialidad', 'maestria' => 'Maestria'] as $prefix => $label)
                    <div class="rounded-lg border border-slate-200 p-4">
                        <h3 class="mb-4 text-sm font-bold text-slate-700">{{ $label }}</h3>
                        <label class="input-label" for="{{ $prefix }}_universidad">Universidad</label>
                        <input id="{{ $prefix }}_universidad" name="{{ $prefix }}_universidad" value="{{ old($prefix.'_universidad', $affiliate->{$prefix.'_universidad'}) }}" class="input-field">
                        <label class="input-label mt-4" for="{{ $prefix }}_anio">Anio</label>
                        <input id="{{ $prefix }}_anio" name="{{ $prefix }}_anio" value="{{ old($prefix.'_anio', $affiliate->{$prefix.'_anio'}) }}" class="input-field">
                        <label class="input-label mt-4" for="{{ $prefix }}_titulo">Titulo</label>
                        <input id="{{ $prefix }}_titulo" name="{{ $prefix }}_titulo" value="{{ old($prefix.'_titulo', $affiliate->{$prefix.'_titulo'}) }}" class="input-field">
                    </div>
                @endforeach
            </div>

            <div class="border-y border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">4. Informacion laboral</h2>
            </div>

            <div class="grid gap-5 p-5 md:grid-cols-2">
                <div>
                    <label class="input-label" for="tipo_item">Tipo de item</label>
                    <select id="tipo_item" name="tipo_item" class="input-field">
                        <option value="">Seleccionar</option>
                        @foreach ($itemTypes as $itemType)
                            <option value="{{ $itemType }}" @selected(old('tipo_item', $affiliate->tipo_item) === $itemType)>{{ $itemType }}</option>
                        @endforeach
                    </select>
                    @error('tipo_item')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label" for="sindicato_id">Sindicato</label>
                    <select id="sindicato_id" name="sindicato_id" class="input-field">
                        <option value="">Afiliado directo FESIRMES</option>
                        @foreach ($sindicatos as $sindicato)
                            <option value="{{ $sindicato->id }}" @selected((int) old('sindicato_id', $affiliate->sindicato_id) === $sindicato->id)>
                                {{ $sindicato->sigla ? $sindicato->sigla.' - ' : '' }}{{ $sindicato->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('sindicato_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label" for="lugar_trabajo">Lugar de trabajo</label>
                    <input id="lugar_trabajo" name="lugar_trabajo" value="{{ old('lugar_trabajo', $affiliate->lugar_trabajo) }}" class="input-field">
                    @error('lugar_trabajo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label" for="red_salud">Red de salud</label>
                    <input id="red_salud" name="red_salud" value="{{ old('red_salud', $affiliate->red_salud) }}" class="input-field">
                    @error('red_salud')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="input-label" for="fecha_ingreso_sistema">Fecha de ingreso al sistema</label>
                    <input id="fecha_ingreso_sistema" name="fecha_ingreso_sistema" type="date" value="{{ old('fecha_ingreso_sistema', $affiliate->fecha_ingreso_sistema?->format('Y-m-d') ?? $affiliate->joined_at?->format('Y-m-d')) }}" class="input-field">
                </div>
                <div>
                    <label class="input-label" for="fecha_primer_descuento_fesirmes">Fecha primer descuento FESIRMES</label>
                    <input id="fecha_primer_descuento_fesirmes" name="fecha_primer_descuento_fesirmes" type="date" value="{{ old('fecha_primer_descuento_fesirmes', $affiliate->fecha_primer_descuento_fesirmes?->format('Y-m-d')) }}" class="input-field">
                </div>
            </div>

            <div class="border-y border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">5. Informacion adicional</h2>
            </div>

            <div class="grid gap-5 p-5 md:grid-cols-2">
                <div>
                    <label class="input-label" for="tematica_capacitacion">Tematica de capacitacion</label>
                    <textarea id="tematica_capacitacion" name="tematica_capacitacion" rows="4" class="input-field">{{ old('tematica_capacitacion', $affiliate->tematica_capacitacion) }}</textarea>
                </div>
                <div>
                    <label class="input-label" for="deportes">Deportes</label>
                    <textarea id="deportes" name="deportes" rows="4" class="input-field">{{ old('deportes', $affiliate->deportes) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="input-label" for="photo">Fotografia</label>
                    <div class="mt-3 flex flex-col gap-4 sm:flex-row sm:items-center">
                        @include('affiliates.partials.photo-avatar', ['affiliate' => $affiliate, 'size' => 'h-24 w-24', 'text' => 'text-2xl'])
                        @unless ($isRestricted)
                            <input id="photo" name="photo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="input-field file:mr-4 file:rounded-md file:border-0 file:bg-cyan-800 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white" data-photo-input>
                        @endunless
                    </div>
                    @error('photo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 text-right">
                @unless ($isRestricted)
                    <button class="btn-primary" type="submit">Guardar cambios</button>
                @endunless
            </div>
            </fieldset>
        </form>

        <aside class="space-y-6">
            <section class="panel overflow-hidden">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <h2 class="section-title">Datos bloqueados</h2>
                </div>
                <dl class="space-y-3 p-5 text-sm">
                    <div><dt class="text-slate-500">Nombre</dt><dd class="font-semibold text-slate-950">{{ $affiliate->full_name }}</dd></div>
                    <div><dt class="text-slate-500">C.I.</dt><dd class="font-semibold text-slate-950">{{ $affiliate->ci }}</dd></div>
                    <div><dt class="text-slate-500">Item principal</dt><dd class="font-semibold text-slate-950">{{ $affiliate->item_principal ?? 'Sin item' }}</dd></div>
                    <div><dt class="text-slate-500">Item secundario</dt><dd class="font-semibold text-slate-950">{{ $affiliate->item_secundario ?? 'Sin item secundario' }}</dd></div>
                </dl>
            </section>
        </aside>
    </div>
</x-app-layout>
