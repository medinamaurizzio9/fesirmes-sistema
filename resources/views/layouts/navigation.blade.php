<div>
    @php($institution = \App\Models\SystemSetting::institutional())
    <div class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-slate-950 transition-transform duration-200 lg:translate-x-0" :class="{ 'translate-x-0': sidebarOpen }">
        <div class="flex h-16 items-center justify-between border-b border-white/10 px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg border border-white/10 bg-white/95 p-1.5 shadow-sm">
                    @if ($institution['system_logo_url'])
                        <img src="{{ $institution['system_logo_url'] }}" alt="Logo FESIRMES" class="h-full w-full object-contain">
                    @else
                        <span class="flex h-full w-full items-center justify-center rounded-md bg-cyan-700 text-sm font-bold text-white">FE</span>
                    @endif
                </span>
                <span>
                    <span class="block text-base font-bold tracking-wide text-white">{{ $institution['institution_acronym'] }}</span>
                    <span class="block text-xs text-slate-400">{{ $institution['institution_subtitle'] ?: 'Gestion institucional' }}</span>
                </span>
            </a>
            <button class="rounded-md p-2 text-slate-300 hover:bg-white/10 lg:hidden" type="button" @click="sidebarOpen = false" aria-label="Cerrar menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 px-4 py-5">
            @if (auth()->user()->role->isAffiliate())
                @php($affiliatePortalRestricted = auth()->user()->affiliate?->hasRestrictedPortalAccess())
                <a href="{{ route('affiliate.profile') }}" class="sidebar-link {{ request()->routeIs('affiliate.profile') ? 'sidebar-link-active' : '' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm-7 8a7 7 0 0 1 14 0" />
                    </svg>
                    Mi Perfil
                </a>
                @unless ($affiliatePortalRestricted)
                <a href="{{ route('affiliate.password.edit') }}" class="sidebar-link {{ request()->routeIs('affiliate.password.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 11V8a4 4 0 0 1 8 0v3m-9 0h10v9H7v-9Z" />
                    </svg>
                    Mi Contraseña
                </a>
                @endunless
            @else
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 13h6V4H4v9Zm10 7h6V4h-6v16ZM4 20h6v-4H4v4Z" />
                </svg>
                Dashboard
            </a>
            <a href="{{ route('afiliados.index') }}" class="sidebar-link {{ request()->routeIs('afiliados.*') ? 'sidebar-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16 11a4 4 0 1 0-8 0m8 0a4 4 0 0 1-8 0m8 0v1a4 4 0 0 0 4 4h1M8 12a4 4 0 0 1-4 4H3m3 4h12M6 20a6 6 0 0 1 12 0" />
                </svg>
                Afiliados
            </a>
            <a href="{{ route('sindicatos.index') }}" class="sidebar-link {{ request()->routeIs('sindicatos.*') ? 'sidebar-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 20V8l8-4 8 4v12M8 20v-7h8v7M6 10l6-3 6 3" />
                </svg>
                Sindicatos
            </a>
            <a href="{{ route('actividades.index') }}" class="sidebar-link {{ request()->routeIs('actividades.*') ? 'sidebar-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 7V3m8 4V3M4 11h16M5 5h14a1 1 0 0 1 1 1v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm3 10h.01M12 15h.01M16 15h.01" />
                </svg>
                Actividades
            </a>
            <a href="{{ route('reportes.index') }}" class="sidebar-link {{ request()->routeIs('reportes.*') ? 'sidebar-link-active' : '' }}">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 19V5m0 14h14M9 16v-5m4 5V8m4 8v-3" />
                </svg>
                Reportes
            </a>
            @if (auth()->user()->role->canModifyCi())
                <a href="{{ route('settings.logo.edit') }}" class="sidebar-link {{ request()->routeIs('settings.*') ? 'sidebar-link-active' : '' }}">
                    <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.6-4.6a2 2 0 0 1 2.8 0L16 16m-2-2 1.6-1.6a2 2 0 0 1 2.8 0L20 14m-16 5h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Zm3-10h.01" />
                    </svg>
                    Logo institucional
                </a>
            @endif
            @endif
        </nav>

        <div class="border-t border-white/10 p-4">
            <div class="rounded-lg bg-white/5 p-3">
                <div class="text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
                <div class="mt-1 text-xs text-slate-400">{{ auth()->user()->role->value }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button class="w-full rounded-md border border-white/10 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-white/10 hover:text-white" type="submit">Salir</button>
            </form>
        </div>
    </aside>

    <div class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 shadow-sm lg:hidden">
        <button class="rounded-md p-2 text-slate-700 hover:bg-slate-100" type="button" @click="sidebarOpen = true" aria-label="Abrir menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 text-base font-bold text-slate-950">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white p-1 shadow-sm">
                @if ($institution['system_logo_url'])
                    <img src="{{ $institution['system_logo_url'] }}" alt="Logo FESIRMES" class="h-full w-full object-contain">
                @else
                    <span class="flex h-full w-full items-center justify-center rounded-md bg-cyan-700 text-xs font-bold text-white">FE</span>
                @endif
            </span>
            {{ $institution['institution_acronym'] }}
        </a>
        <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-800">{{ auth()->user()->role->value }}</span>
    </div>
</div>
