<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Configuracion</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Logo institucional</h1>
            <p class="mt-1 text-sm text-slate-600">Este logo se usa en el sistema y en las credenciales digitales.</p>
        </div>
    </x-slot>

    <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
        <section class="panel overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Cargar logo</h2>
                <p class="mt-1 text-sm text-slate-500">Usa PNG con fondo transparente para mejores resultados en Canva.</p>
            </div>

            <form method="POST" action="{{ route('settings.logo.update') }}" enctype="multipart/form-data" class="space-y-5 p-5">
                @csrf
                <div>
                    <label class="input-label" for="logo">Archivo de logo</label>
                    <input id="logo" name="logo" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="input-field file:mr-4 file:rounded-md file:border-0 file:bg-cyan-800 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-white" required>
                    @error('logo')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-2 text-xs text-slate-500">Formatos permitidos: JPG, PNG o WEBP. Tamano maximo: 2MB.</p>
                </div>

                <div class="flex flex-col-reverse gap-3 border-t border-slate-200 pt-5 sm:flex-row sm:justify-end">
                    @if ($logoPath)
                        <a href="{{ route('settings.logo.png') }}" class="btn-secondary">Descargar logo PNG</a>
                    @endif
                    <button type="submit" class="btn-primary">Guardar logo</button>
                </div>
            </form>
        </section>

        <aside class="panel h-fit overflow-hidden">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                <h2 class="section-title">Vista actual</h2>
            </div>
            <div class="flex min-h-52 items-center justify-center p-5">
                @if ($logoPath)
                    <img src="{{ route('system.logo') }}" alt="Logo institucional actual" class="max-h-36 max-w-full object-contain">
                @else
                    <div class="flex h-24 w-24 items-center justify-center rounded-lg bg-cyan-800 text-2xl font-bold text-white">FE</div>
                @endif
            </div>
        </aside>
    </div>
</x-app-layout>
