<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reportes internos</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Reportes FESIRMES</h1>
            <p class="mt-1 text-sm text-slate-600">Consultas internas para seguimiento institucional y control operativo.</p>
        </div>
    </x-slot>

    <section class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        @foreach ([
            ['route' => 'reportes.padron', 'title' => 'Padron general', 'text' => 'Afiliados con filtros institucionales.'],
            ['route' => 'reportes.quality', 'title' => 'Calidad de datos', 'text' => 'Registros incompletos para correccion.'],
            ['route' => 'reportes.sindicatos', 'title' => 'Por sindicato', 'text' => 'Totales y estados por sindicato.'],
            ['route' => 'reportes.item-types', 'title' => 'Por tipo de item', 'text' => 'Distribucion SEDES, Ministerial, INLASA y SEDEGES.'],
            ['route' => 'reportes.attendance.activities', 'title' => 'Asistencia por actividad', 'text' => 'Resumen de validez por evento.'],
            ['route' => 'reportes.attendance.history', 'title' => 'Historico por afiliado', 'text' => 'Porcentaje de asistencia por afiliado.'],
        ] as $report)
            <a href="{{ route($report['route']) }}" class="panel block p-5 transition hover:border-cyan-200 hover:bg-cyan-50">
                <h2 class="text-lg font-bold text-slate-950">{{ $report['title'] }}</h2>
                <p class="mt-2 text-sm text-slate-600">{{ $report['text'] }}</p>
                <div class="mt-4 text-sm font-semibold text-cyan-800">Abrir reporte</div>
            </a>
        @endforeach
    </section>
</x-app-layout>
