<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Configuracion</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Configuracion institucional</h1>
            <p class="mt-1 text-sm text-slate-600">Datos visibles en login, sistema, credenciales y reportes PDF.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
        <section class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Datos institucionales</h2>
            </div>

            <form method="POST" action="{{ route('settings.logo.update') }}" enctype="multipart/form-data" class="space-y-5 p-5">
                @csrf
                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="input-label" for="institution_name">Nombre institucion</label>
                        <input id="institution_name" name="institution_name" value="{{ old('institution_name', $settings['institution_name']) }}" class="input-field" required>
                    </div>
                    <div>
                        <label class="input-label" for="institution_acronym">Sigla</label>
                        <input id="institution_acronym" name="institution_acronym" value="{{ old('institution_acronym', $settings['institution_acronym']) }}" class="input-field" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="input-label" for="institution_subtitle">Subtitulo institucional</label>
                        <input id="institution_subtitle" name="institution_subtitle" value="{{ old('institution_subtitle', $settings['institution_subtitle']) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label" for="institution_address">Direccion</label>
                        <input id="institution_address" name="institution_address" value="{{ old('institution_address', $settings['institution_address']) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label" for="institution_phones">Telefonos</label>
                        <input id="institution_phones" name="institution_phones" value="{{ old('institution_phones', $settings['institution_phones']) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label" for="institution_email">Correo institucional</label>
                        <input id="institution_email" name="institution_email" type="email" value="{{ old('institution_email', $settings['institution_email']) }}" class="input-field">
                    </div>
                    <div>
                        <label class="input-label" for="institution_website">Sitio web</label>
                        <input id="institution_website" name="institution_website" value="{{ old('institution_website', $settings['institution_website']) }}" class="input-field">
                    </div>
                    <div class="md:col-span-2">
                        <label class="input-label" for="pdf_footer">Pie de pagina para PDFs</label>
                        <input id="pdf_footer" name="pdf_footer" value="{{ old('pdf_footer', $settings['pdf_footer']) }}" class="input-field">
                    </div>
                    <div class="md:col-span-2">
                        <label class="input-label" for="logo">Logo institucional</label>
                        <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="input-field file:mr-4 file:rounded-md file:border-0 file:bg-cyan-800 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white">
                        @error('logo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                        <p class="mt-2 text-xs text-slate-500">Si no cargas un nuevo logo, se conserva el actual.</p>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    @if ($logoUrl)
                        <a href="{{ route('settings.logo.png') }}" class="btn-secondary">Descargar logo PNG</a>
                    @endif
                    <button type="submit" class="btn-primary">Guardar configuracion</button>
                </div>
            </form>
        </section>

        <aside class="panel h-fit overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Vista institucional</h2>
            </div>
            <div class="p-5 text-center">
                <div class="flex min-h-32 items-center justify-center">
                    @if ($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo institucional actual" class="max-h-28 max-w-full object-contain">
                    @else
                        <div class="flex h-24 w-24 items-center justify-center rounded-lg bg-cyan-800 text-2xl font-bold text-white">FE</div>
                    @endif
                </div>
                <div class="mt-4 text-lg font-bold text-slate-950">{{ $settings['institution_name'] }}</div>
                <div class="mt-1 text-sm text-slate-500">{{ $settings['institution_subtitle'] }}</div>
            </div>
        </aside>
    </div>
</x-app-layout>
