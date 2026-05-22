<x-guest-layout>
    <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">
        <h2 class="text-lg font-bold text-slate-950">Ingreso al sistema</h2>
        <p class="mt-1 text-sm text-slate-600">Accede con tu usuario asignado.</p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="space-y-5 p-6">
        @csrf

        <div>
            <label class="input-label" for="email">Correo electronico</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" class="input-field" required autofocus autocomplete="username">
            @error('email')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="input-label" for="password">Contrasena</label>
            <input id="password" name="password" type="password" class="input-field" required autocomplete="current-password">
            @error('password')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex items-center gap-2 text-sm text-slate-600">
            <input type="checkbox" name="remember" class="rounded border-slate-300 text-slate-900 focus:ring-slate-500">
            Recordarme
        </label>

        <button class="btn-primary w-full" type="submit">Ingresar</button>

        <div class="rounded-md border border-slate-200 bg-slate-50 p-3 text-xs leading-5 text-slate-600">
            <p><strong>Pruebas:</strong> admin@fesirmes.local / password</p>
            <p>secretaria@fesirmes.local / password</p>
            <p>consulta@fesirmes.local / password</p>
        </div>
    </form>
</x-guest-layout>
