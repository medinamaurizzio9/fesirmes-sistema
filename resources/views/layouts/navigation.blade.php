<div>
    <div class="fixed inset-0 z-40 bg-slate-950/40 lg:hidden" x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"></div>

    <aside class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col bg-slate-950 transition-transform duration-200 lg:translate-x-0" :class="{ 'translate-x-0': sidebarOpen }">
        <div class="flex h-16 items-center justify-between border-b border-white/10 px-5">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-md bg-cyan-700 text-sm font-bold text-white">FE</span>
                <span>
                    <span class="block text-base font-bold tracking-wide text-white">FESIRMES</span>
                    <span class="block text-xs text-slate-400">Gestion institucional</span>
                </span>
            </a>
            <button class="rounded-md p-2 text-slate-300 hover:bg-white/10 lg:hidden" type="button" @click="sidebarOpen = false" aria-label="Cerrar menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 px-4 py-5">
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
        <a href="{{ route('dashboard') }}" class="text-base font-bold text-slate-950">FESIRMES</a>
        <span class="rounded-full bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-800">{{ auth()->user()->role->value }}</span>
    </div>
</div>
