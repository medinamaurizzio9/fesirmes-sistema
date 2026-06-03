<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Portal del afiliado</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Mi Contraseña</h1>
            <p class="mt-1 text-sm text-slate-600">La nueva contraseña debe tener minimo 8 caracteres y no puede ser igual a tu C.I.</p>
        </div>
    </x-slot>

    <form method="POST" action="{{ route('affiliate.password.update') }}" class="panel mx-auto max-w-xl overflow-hidden">
        @csrf
        @method('PUT')

        <div class="space-y-5 p-5">
            <div>
                <label class="input-label" for="current_password">Contraseña actual</label>
                <input id="current_password" name="current_password" type="password" class="input-field" required autocomplete="current-password">
                @error('current_password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="input-label" for="password">Nueva contraseña</label>
                <input id="password" name="password" type="password" class="input-field" required autocomplete="new-password">
                @error('password')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="input-label" for="password_confirmation">Confirmar contraseña</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="input-field" required autocomplete="new-password">
            </div>
        </div>

        <div class="border-t border-slate-200 bg-slate-50 px-5 py-4 text-right">
            <button class="btn-primary" type="submit">Actualizar contraseña</button>
        </div>
    </form>
</x-app-layout>
