<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateStatus;
use App\Models\Activity;
use App\Models\Affiliate;
use App\Models\Attendance;
use App\Models\Sindicato;
use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class InternalReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index');
    }

    public function padron(Request $request): View
    {
        return view('reports.padron', array_merge($this->sharedFilters(), [
            'affiliates' => $this->padronQuery($request)->paginate(25)->withQueryString(),
            'canExport' => $this->canExportOperational(),
        ]));
    }

    public function padronCsv(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('padron_general', 'csv', $request);

        $rows = $this->padronQuery($request)->get()->map(fn (Affiliate $affiliate) => [
            $affiliate->full_name,
            $affiliate->ci,
            $affiliate->item_principal,
            $affiliate->tipo_item,
            $affiliate->sindicato?->nombre,
            $affiliate->status?->value,
            $affiliate->celular ?? $affiliate->phone,
            $affiliate->lugar_trabajo,
            $affiliate->red_salud,
        ]);

        return $this->csvDownload('padron-general-fesirmes.csv', ['Nombre completo', 'C.I.', 'Item principal', 'Tipo item', 'Sindicato', 'Estado', 'Celular', 'Lugar trabajo', 'Red salud'], $rows);
    }

    public function padronPdf(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('padron_general', 'pdf', $request);

        $headers = ['Nombre', 'C.I.', 'Item', 'Tipo', 'Sindicato', 'Estado', 'Celular'];
        $rows = $this->padronQuery($request)->limit(700)->get()->map(fn (Affiliate $affiliate) => [
            $affiliate->full_name,
            $affiliate->ci,
            $affiliate->item_principal,
            $affiliate->tipo_item,
            $affiliate->sindicato?->sigla ?? $affiliate->sindicato?->nombre,
            $affiliate->status?->value,
            $affiliate->celular ?? $affiliate->phone,
        ]);

        return $this->pdfDownload('padron-general-fesirmes.pdf', 'Padron general', $headers, $rows, $request, 'a4', 'landscape');
    }

    public function quality(Request $request): View
    {
        $categories = $this->qualityCategories();
        $selected = $this->selectedQualityCategory($request, $categories);

        return view('reports.quality', array_merge($this->sharedFilters(), [
            'categories' => $categories,
            'selected' => $selected,
            'counts' => collect($categories)->map(fn (array $category, string $key) => $this->qualityQuery($request, $key)->count()),
            'affiliates' => $this->qualityQuery($request, $selected)->with('sindicato')->paginate(20)->withQueryString(),
            'canExport' => $this->canExportOperational(),
        ]));
    }

    public function qualityCsv(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('calidad_datos', 'csv', $request);

        $categories = $this->qualityCategories();
        $selected = $this->selectedQualityCategory($request, $categories);
        $rows = $this->qualityQuery($request, $selected)->with('sindicato')->get()->map(fn (Affiliate $affiliate) => [
            $categories[$selected]['label'],
            $affiliate->full_name,
            $affiliate->ci,
            $affiliate->status?->value,
            $affiliate->tipo_item,
            $affiliate->sindicato?->nombre,
            $affiliate->celular ?? $affiliate->phone,
        ]);

        return $this->csvDownload('calidad-datos-fesirmes.csv', ['Problema', 'Nombre completo', 'C.I.', 'Estado', 'Tipo item', 'Sindicato', 'Telefono'], $rows);
    }

    public function qualityPdf(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('calidad_datos', 'pdf', $request);

        $categories = $this->qualityCategories();
        $selected = $this->selectedQualityCategory($request, $categories);
        $headers = ['Problema', 'Nombre', 'C.I.', 'Estado', 'Tipo', 'Sindicato'];
        $rows = $this->qualityQuery($request, $selected)->with('sindicato')->limit(700)->get()->map(fn (Affiliate $affiliate) => [
            $categories[$selected]['label'],
            $affiliate->full_name,
            $affiliate->ci,
            $affiliate->status?->value,
            $affiliate->tipo_item,
            $affiliate->sindicato?->sigla ?? $affiliate->sindicato?->nombre,
        ]);

        return $this->pdfDownload('calidad-datos-fesirmes.pdf', 'Calidad de datos', $headers, $rows, $request, 'a4', 'landscape');
    }

    public function sindicatos(Request $request): View
    {
        return view('reports.sindicatos', array_merge($this->sharedFilters(), [
            'sindicatos' => $this->sindicatoReportQuery($request)->paginate(20)->withQueryString(),
            'sindicatoOptions' => Sindicato::orderBy('nombre')->get(),
            'total' => max($this->filteredAffiliatesForSindicatoReport($request)->count(), 1),
            'canExport' => $this->canExportOperational(),
            'sindicatoStatuses' => Sindicato::statuses(),
        ]));
    }

    public function sindicatosCsv(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('por_sindicato', 'csv', $request);
        $total = max($this->filteredAffiliatesForSindicatoReport($request)->count(), 1);
        $rows = $this->sindicatoReportQuery($request)->get()->map(fn (Sindicato $sindicato) => [
            $sindicato->nombre,
            $sindicato->sigla,
            $sindicato->total_afiliados,
            $sindicato->activos,
            $sindicato->bajas,
            $sindicato->suspendidos,
            $sindicato->observados,
            round(($sindicato->total_afiliados / $total) * 100, 2).'%',
        ]);

        return $this->csvDownload('reporte-sindicatos-fesirmes.csv', ['Sindicato', 'Sigla', 'Total', 'Activos', 'Bajas', 'Suspendidos', 'Observados', 'Porcentaje'], $rows);
    }

    public function sindicatosPdf(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('por_sindicato', 'pdf', $request);
        $total = max($this->filteredAffiliatesForSindicatoReport($request)->count(), 1);
        $rows = $this->sindicatoReportQuery($request)->limit(700)->get()->map(fn (Sindicato $sindicato) => [
            $sindicato->nombre,
            $sindicato->sigla,
            $sindicato->total_afiliados,
            $sindicato->activos,
            $sindicato->bajas,
            $sindicato->suspendidos,
            $sindicato->observados,
            round(($sindicato->total_afiliados / $total) * 100, 2).'%',
        ]);

        return $this->pdfDownload('reporte-sindicatos-fesirmes.pdf', 'Reporte por sindicato', ['Sindicato', 'Sigla', 'Total', 'Activos', 'Bajas', 'Susp.', 'Obs.', '%'], $rows, $request, 'a4', 'landscape');
    }

    public function itemTypes(Request $request): View
    {
        return view('reports.item-types', array_merge($this->sharedFilters(), [
            'counts' => $this->itemTypeRows($request),
            'total' => max($this->filteredAffiliatesForItemTypeReport($request)->count(), 1),
            'canExport' => $this->canExportOperational(),
        ]));
    }

    public function itemTypesCsv(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('por_tipo_item', 'csv', $request);
        $total = max($this->filteredAffiliatesForItemTypeReport($request)->count(), 1);
        $rows = $this->itemTypeRows($request)->map(fn (int $count, string $type) => [$type, $count, round(($count / $total) * 100, 2).'%']);

        return $this->csvDownload('reporte-tipos-item-fesirmes.csv', ['Tipo item', 'Total afiliados', 'Porcentaje'], $rows);
    }

    public function itemTypesPdf(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('por_tipo_item', 'pdf', $request);
        $total = max($this->filteredAffiliatesForItemTypeReport($request)->count(), 1);
        $rows = $this->itemTypeRows($request)->map(fn (int $count, string $type) => [$type, $count, round(($count / $total) * 100, 2).'%']);

        return $this->pdfDownload('reporte-tipos-item-fesirmes.pdf', 'Reporte por tipo de item', ['Tipo item', 'Total', 'Porcentaje'], $rows, $request);
    }

    public function attendanceActivities(Request $request): View
    {
        return view('reports.attendance-activities', array_merge($this->sharedFilters(), [
            'activities' => $this->attendanceActivitiesQuery($request)->paginate(20)->withQueryString(),
            'activityOptions' => Activity::latest('fecha')->get(),
            'activeAffiliates' => $this->activeAffiliatesForAttendance($request),
            'canExport' => $this->canExportOperational(),
        ]));
    }

    public function attendanceActivitiesCsv(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('asistencia_por_actividad', 'csv', $request);
        $activeAffiliates = $this->activeAffiliatesForAttendance($request);
        $rows = $this->attendanceActivitiesQuery($request)->get()->map(fn (Activity $activity) => [
            $activity->nombre,
            $activity->fecha?->format('d/m/Y'),
            $activity->estado,
            $activeAffiliates,
            $activity->validos,
            $activity->duplicados,
            $activity->observados,
            $activity->invalidos,
            ($activeAffiliates > 0 ? round(($activity->validos / $activeAffiliates) * 100, 2) : 0).'%',
        ]);

        return $this->csvDownload('asistencia-por-actividad-fesirmes.csv', ['Actividad', 'Fecha', 'Estado', 'Activos', 'Validos', 'Duplicados', 'Observados', 'Invalidos', 'Porcentaje'], $rows);
    }

    public function attendanceActivitiesPdf(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('asistencia_por_actividad', 'pdf', $request);
        $activeAffiliates = $this->activeAffiliatesForAttendance($request);
        $rows = $this->attendanceActivitiesQuery($request)->limit(700)->get()->map(fn (Activity $activity) => [
            $activity->nombre,
            $activity->fecha?->format('d/m/Y'),
            $activity->estado,
            $activeAffiliates,
            $activity->validos,
            $activity->duplicados,
            $activity->observados,
            $activity->invalidos,
            ($activeAffiliates > 0 ? round(($activity->validos / $activeAffiliates) * 100, 2) : 0).'%',
        ]);

        return $this->pdfDownload('asistencia-por-actividad-fesirmes.pdf', 'Asistencia por actividad', ['Actividad', 'Fecha', 'Estado', 'Activos', 'Val.', 'Dup.', 'Obs.', 'Inv.', '%'], $rows, $request, 'a4', 'landscape');
    }

    public function attendanceHistory(Request $request): View
    {
        $report = $this->attendanceHistoryData($request, true);

        return view('reports.attendance-history', array_merge($this->sharedFilters(), [
            'affiliates' => $report['affiliates'],
            'totalRealizadas' => $report['totalRealizadas'],
            'validCounts' => $report['validCounts'],
            'canExport' => $this->canExportOperational(),
        ]));
    }

    public function attendanceHistoryCsv(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('historico_asistencia_afiliado', 'csv', $request);
        $report = $this->attendanceHistoryData($request, false);
        $rows = $report['affiliates']->map(fn (Affiliate $affiliate) => $this->attendanceHistoryRow($affiliate, $report['totalRealizadas'], $report['validCounts']));

        return $this->csvDownload('historico-asistencia-afiliado-fesirmes.csv', ['Nombre', 'C.I.', 'Item', 'Sindicato', 'Actividades realizadas', 'Asistencias validas', 'Porcentaje'], $rows);
    }

    public function attendanceHistoryPdf(Request $request)
    {
        $this->authorizeExport();
        $this->auditExport('historico_asistencia_afiliado', 'pdf', $request);
        $report = $this->attendanceHistoryData($request, false);
        $rows = $report['affiliates']->take(700)->map(fn (Affiliate $affiliate) => $this->attendanceHistoryRow($affiliate, $report['totalRealizadas'], $report['validCounts']));

        return $this->pdfDownload('historico-asistencia-afiliado-fesirmes.pdf', 'Historico de asistencia por afiliado', ['Nombre', 'C.I.', 'Item', 'Sindicato', 'Realizadas', 'Validas', '%'], $rows, $request, 'a4', 'landscape');
    }

    private function padronQuery(Request $request): Builder
    {
        return $this->affiliateQuery($request)
            ->when($request->string('lugar_trabajo')->toString(), fn ($query, string $term) => $query->where('lugar_trabajo', 'like', "%{$term}%"))
            ->when($request->string('red_salud')->toString(), fn ($query, string $term) => $query->where('red_salud', 'like', "%{$term}%"))
            ->orderBy('apellido_paterno')
            ->orderBy('nombres');
    }

    private function affiliateQuery(Request $request): Builder
    {
        return Affiliate::query()
            ->with('sindicato')
            ->when($request->string('buscar')->toString(), fn ($query, string $term) => $query->where(fn ($subquery) => $subquery
                ->where('ci', 'like', "%{$term}%")
                ->orWhere('nombres', 'like', "%{$term}%")
                ->orWhere('apellido_paterno', 'like', "%{$term}%")
                ->orWhere('apellido_materno', 'like', "%{$term}%")
                ->orWhere('first_name', 'like', "%{$term}%")
                ->orWhere('last_name', 'like', "%{$term}%")))
            ->when($request->string('estado')->toString(), fn ($query, string $estado) => $query->where('status', $estado))
            ->when($request->string('tipo_item')->toString(), fn ($query, string $tipoItem) => $query->where('tipo_item', $tipoItem))
            ->when($request->integer('sindicato_id'), fn ($query, int $sindicatoId) => $query->where('sindicato_id', $sindicatoId));
    }

    private function qualityQuery(Request $request, string $category): Builder
    {
        return $this->applyQualityProblem($this->affiliateQuery($request), $category)
            ->orderBy('apellido_paterno')
            ->orderBy('nombres');
    }

    private function applyQualityProblem(Builder $query, string $category): Builder
    {
        return match ($category) {
            'sin_foto' => $query->whereNull('photo_path'),
            'sin_celular' => $query->where(fn ($subquery) => $subquery->whereNull('celular')->orWhere('celular', '')),
            'sin_email' => $query->where(fn ($subquery) => $subquery->whereNull('email')->orWhere('email', '')),
            'sin_lugar_trabajo' => $query->where(fn ($subquery) => $subquery->whereNull('lugar_trabajo')->orWhere('lugar_trabajo', '')),
            'sin_red_salud' => $query->where(fn ($subquery) => $subquery->whereNull('red_salud')->orWhere('red_salud', '')),
            'sin_item_principal' => $query->where(fn ($subquery) => $subquery->whereNull('item_principal')->orWhere('item_principal', '')),
            'sin_sindicato' => $query->whereNull('sindicato_id'),
            'minimos_incompletos' => $query->where(fn ($subquery) => $subquery
                ->whereNull('ci')->orWhere('ci', '')
                ->orWhereNull('nombres')->orWhere('nombres', '')
                ->orWhereNull('apellido_paterno')->orWhere('apellido_paterno', '')
                ->orWhereNull('status')),
            default => $query,
        };
    }

    private function qualityCategories(): array
    {
        return [
            'sin_foto' => ['label' => 'Afiliados sin foto'],
            'sin_celular' => ['label' => 'Sin celular'],
            'sin_email' => ['label' => 'Sin email'],
            'sin_lugar_trabajo' => ['label' => 'Sin lugar de trabajo'],
            'sin_red_salud' => ['label' => 'Sin red de salud'],
            'sin_item_principal' => ['label' => 'Sin item principal'],
            'sin_sindicato' => ['label' => 'Sin sindicato asignado'],
            'minimos_incompletos' => ['label' => 'Datos minimos incompletos'],
        ];
    }

    private function selectedQualityCategory(Request $request, array $categories): string
    {
        $selected = $request->string('categoria')->toString() ?: array_key_first($categories);

        return array_key_exists($selected, $categories) ? $selected : array_key_first($categories);
    }

    private function sindicatoReportQuery(Request $request): Builder
    {
        $affiliateFilter = fn ($query) => $query
            ->when($request->string('estado_afiliado')->toString(), fn ($subquery, string $status) => $subquery->where('status', $status))
            ->when($request->string('tipo_item')->toString(), fn ($subquery, string $type) => $subquery->where('tipo_item', $type));

        return Sindicato::query()
            ->when($request->string('estado_sindicato')->toString(), fn ($query, string $estado) => $query->where('estado', $estado))
            ->when($request->integer('sindicato_id'), fn ($query, int $id) => $query->where('id', $id))
            ->withCount([
                'affiliates as total_afiliados' => $affiliateFilter,
                'affiliates as activos' => fn ($query) => $affiliateFilter($query)->where('status', 'activo'),
                'affiliates as bajas' => fn ($query) => $affiliateFilter($query)->where('status', 'baja'),
                'affiliates as suspendidos' => fn ($query) => $affiliateFilter($query)->where('status', 'suspendido'),
                'affiliates as observados' => fn ($query) => $affiliateFilter($query)->where('status', 'observado'),
            ])
            ->orderBy('nombre');
    }

    private function filteredAffiliatesForSindicatoReport(Request $request): Builder
    {
        return Affiliate::query()
            ->when($request->integer('sindicato_id'), fn ($query, int $id) => $query->where('sindicato_id', $id))
            ->when($request->string('estado_afiliado')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->string('tipo_item')->toString(), fn ($query, string $type) => $query->where('tipo_item', $type))
            ->when($request->string('estado_sindicato')->toString(), fn ($query, string $estado) => $query->whereHas('sindicato', fn ($subquery) => $subquery->where('estado', $estado)));
    }

    private function itemTypeRows(Request $request): Collection
    {
        return collect(Affiliate::itemTypes())
            ->when($request->string('tipo_item')->toString(), fn ($types, string $type) => $types->filter(fn ($item) => $item === $type))
            ->mapWithKeys(fn (string $type) => [
                $type => $this->filteredAffiliatesForItemTypeReport($request)->where('tipo_item', $type)->count(),
            ]);
    }

    private function filteredAffiliatesForItemTypeReport(Request $request): Builder
    {
        return Affiliate::query()
            ->when($request->string('estado')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->integer('sindicato_id'), fn ($query, int $id) => $query->where('sindicato_id', $id))
            ->when($request->string('tipo_item')->toString(), fn ($query, string $type) => $query->where('tipo_item', $type));
    }

    private function attendanceActivitiesQuery(Request $request): Builder
    {
        $attendanceFilter = fn ($query) => $query
            ->whereNull('reverted_at')
            ->when($request->integer('sindicato_id'), fn ($subquery, int $id) => $subquery->whereHas('affiliate', fn ($affiliateQuery) => $affiliateQuery->where('sindicato_id', $id)))
            ->when($request->string('tipo_item')->toString(), fn ($subquery, string $type) => $subquery->whereHas('affiliate', fn ($affiliateQuery) => $affiliateQuery->where('tipo_item', $type)));

        return Activity::query()
            ->when($request->integer('activity_id'), fn ($query, int $id) => $query->where('id', $id))
            ->when($request->string('estado_actividad')->toString(), fn ($query, string $estado) => $query->where('estado', $estado))
            ->when($request->date('fecha_desde'), fn ($query, $date) => $query->whereDate('fecha', '>=', $date))
            ->when($request->date('fecha_hasta'), fn ($query, $date) => $query->whereDate('fecha', '<=', $date))
            ->withCount([
                'attendances as validos' => fn ($query) => $attendanceFilter($query)->where('estado', 'valido'),
                'attendances as duplicados' => fn ($query) => $attendanceFilter($query)->where('estado', 'duplicado'),
                'attendances as observados' => fn ($query) => $attendanceFilter($query)->where('estado', 'observado'),
                'attendances as invalidos' => fn ($query) => $attendanceFilter($query)->where('estado', 'invalido'),
            ])
            ->latest('fecha');
    }

    private function activeAffiliatesForAttendance(Request $request): int
    {
        return Affiliate::where('status', 'activo')
            ->when($request->integer('sindicato_id'), fn ($query, int $id) => $query->where('sindicato_id', $id))
            ->when($request->string('tipo_item')->toString(), fn ($query, string $type) => $query->where('tipo_item', $type))
            ->count();
    }

    private function attendanceHistoryData(Request $request, bool $paginate): array
    {
        $totalRealizadas = Activity::where('estado', 'realizada')->count();
        $validCounts = Attendance::where('estado', 'valido')
            ->whereNull('reverted_at')
            ->whereHas('activity', fn ($query) => $query->where('estado', 'realizada'))
            ->selectRaw('affiliate_id, COUNT(*) as total')
            ->groupBy('affiliate_id')
            ->pluck('total', 'affiliate_id');

        $affiliates = $this->affiliateQuery($request)
            ->orderBy('apellido_paterno')
            ->orderBy('nombres')
            ->get()
            ->filter(function (Affiliate $affiliate) use ($request, $validCounts, $totalRealizadas) {
                $percent = $totalRealizadas > 0 ? (($validCounts[$affiliate->id] ?? 0) / $totalRealizadas) * 100 : 0;
                $min = $request->filled('porcentaje_min') ? (float) $request->input('porcentaje_min') : null;
                $max = $request->filled('porcentaje_max') ? (float) $request->input('porcentaje_max') : null;

                return ($min === null || $percent >= $min) && ($max === null || $percent <= $max);
            })
            ->values();

        return [
            'affiliates' => $paginate ? $this->paginateCollection($affiliates, 25) : $affiliates,
            'totalRealizadas' => $totalRealizadas,
            'validCounts' => $validCounts,
        ];
    }

    private function attendanceHistoryRow(Affiliate $affiliate, int $totalRealizadas, Collection $validCounts): array
    {
        $validas = $validCounts[$affiliate->id] ?? 0;

        return [
            $affiliate->full_name,
            $affiliate->ci,
            $affiliate->item_principal,
            $affiliate->sindicato?->sigla ?? $affiliate->sindicato?->nombre,
            $totalRealizadas,
            $validas,
            ($totalRealizadas > 0 ? round(($validas / $totalRealizadas) * 100, 2) : 0).'%',
        ];
    }

    private function paginateCollection(Collection $items, int $perPage): LengthAwarePaginator
    {
        $page = Paginator::resolveCurrentPage();

        return new Paginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath(), 'query' => request()->query()]
        );
    }

    private function sharedFilters(): array
    {
        return [
            'statuses' => AffiliateStatus::cases(),
            'itemTypes' => Affiliate::itemTypes(),
            'sindicatos' => Sindicato::orderBy('nombre')->get(),
            'activities' => Activity::latest('fecha')->get(),
            'activityStatuses' => ['programada', 'realizada', 'cancelada'],
        ];
    }

    private function csvDownload(string $filename, array $headers, Collection $rows)
    {
        $csv = "\xEF\xBB\xBF".$this->csvLine($headers);
        foreach ($rows as $row) {
            $csv .= $this->csvLine($row);
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    private function pdfDownload(string $filename, string $title, array $headers, Collection $rows, Request $request, string $paper = 'a4', string $orientation = 'portrait')
    {
        $pdf = Pdf::loadView('reports.exports.table-pdf', [
            'title' => $title,
            'headers' => $headers,
            'rows' => $rows,
            'filters' => $this->activeFilters($request),
            'generatedAt' => now(),
            'generatedBy' => auth()->user()?->name,
            'logoDataUri' => $this->systemLogoDataUri(),
            'year' => now()->year,
        ])->setPaper($paper, $orientation);

        return $pdf->download($filename);
    }

    private function activeFilters(Request $request): array
    {
        return collect($request->query())
            ->reject(fn ($value) => $value === null || $value === '')
            ->map(fn ($value) => is_array($value) ? implode(', ', $value) : $value)
            ->all();
    }

    private function csvLine(array|Collection $values): string
    {
        return collect($values)
            ->map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"')
            ->implode(',')."\n";
    }

    private function canExportOperational(): bool
    {
        return auth()->user()?->role->canManageAffiliates() ?? false;
    }

    private function authorizeExport(): void
    {
        abort_unless($this->canExportOperational(), 403);
    }

    private function auditExport(string $report, string $format, Request $request): void
    {
        AuditLogger::record('reporte.exportado', null, [], [
            'reporte' => $report,
            'formato' => $format,
            'filtros' => $this->activeFilters($request),
            'fecha_hora' => now()->toDateTimeString(),
        ]);
    }

    private function systemLogoDataUri(): ?string
    {
        $path = SystemSetting::getValue('system_logo_path');

        if (! $path || ! Storage::disk('local')->exists($path)) {
            return null;
        }

        $absolutePath = Storage::disk('local')->path($path);
        $mime = mime_content_type($absolutePath) ?: 'image/png';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($absolutePath));
    }
}
