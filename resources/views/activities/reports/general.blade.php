<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-800">Reporte general</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-950">Asistencia por afiliado</h1>
            <p class="mt-1 text-sm text-slate-600">Solo actividades realizadas. Total: {{ $totalRealizadas }}</p>
        </div>
    </x-slot>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-sm">
                <thead class="bg-slate-50 text-left text-xs uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-5 py-3">Afiliado</th>
                        <th class="px-5 py-3">C.I.</th>
                        <th class="px-5 py-3">Item</th>
                        <th class="px-5 py-3">Asistencias</th>
                        <th class="px-5 py-3">Porcentaje</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($affiliates as $affiliate)
                        @php
                            $total = $validCounts[$affiliate->id] ?? 0;
                            $percent = $totalRealizadas > 0 ? round(($total / $totalRealizadas) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td class="px-5 py-4 font-semibold">{{ $affiliate->full_name }}</td>
                            <td class="px-5 py-4">{{ $affiliate->ci }}</td>
                            <td class="px-5 py-4">{{ $affiliate->item_principal ?? 'Sin item' }}</td>
                            <td class="px-5 py-4">{{ $total }} / {{ $totalRealizadas }}</td>
                            <td class="px-5 py-4">{{ $percent }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">{{ $affiliates->links() }}</div>
    </div>
</x-app-layout>
