<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportAttendanceRequest;
use App\Models\Activity;
use App\Models\Affiliate;
use App\Models\Attendance;
use App\Models\Sindicato;
use App\Services\AttendanceCsvImporter;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Activity $activity, Request $request): View
    {
        $sindicatoId = $request->integer('sindicato_id');
        $attendances = $activity->attendances()
            ->with('affiliate.sindicato')
            ->whereNull('reverted_at')
            ->when($sindicatoId, fn ($query) => $query->whereHas('affiliate', fn ($subquery) => $subquery->where('sindicato_id', $sindicatoId)))
            ->latest('imported_at')
            ->paginate(20)
            ->withQueryString();

        $summary = $this->summary($activity, $sindicatoId);
        $batches = $activity->attendances()
            ->whereNull('reverted_at')
            ->selectRaw('import_batch_id, source_file_name, MIN(imported_at) as imported_at, COUNT(*) as total_rows')
            ->groupBy('import_batch_id', 'source_file_name')
            ->latest('imported_at')
            ->get();
        $sindicatos = Sindicato::orderBy('nombre')->get();

        return view('activities.attendances.index', compact('activity', 'attendances', 'summary', 'batches', 'sindicatos'));
    }

    public function importForm(Activity $activity): View
    {
        $this->authorizeManage();

        return view('activities.attendances.import', compact('activity'));
    }

    public function import(ImportAttendanceRequest $request, Activity $activity, AttendanceCsvImporter $importer): View
    {
        $summary = $importer->import($activity, $request->file('csv_file'), $request->input('ci_column'));

        AuditLogger::record('asistencia.importada', $activity, [], $summary);

        return view('activities.attendances.summary', compact('activity', 'summary'));
    }

    public function report(Activity $activity, Request $request): View
    {
        $sindicatoId = $request->integer('sindicato_id');
        $totalActivos = Affiliate::where('status', 'activo')
            ->when($sindicatoId, fn ($query) => $query->where('sindicato_id', $sindicatoId))
            ->count();
        $validos = $activity->attendances()
            ->where('estado', 'valido')
            ->whereNull('reverted_at')
            ->when($sindicatoId, fn ($query) => $query->whereHas('affiliate', fn ($subquery) => $subquery->where('sindicato_id', $sindicatoId)))
            ->with('affiliate.sindicato')
            ->get();
        $revisiones = $activity->attendances()
            ->whereIn('estado', ['invalido', 'observado', 'duplicado'])
            ->whereNull('reverted_at')
            ->when($sindicatoId, fn ($query) => $query->whereHas('affiliate', fn ($subquery) => $subquery->where('sindicato_id', $sindicatoId)))
            ->with('affiliate.sindicato')
            ->get();
        $porcentaje = $totalActivos > 0 ? round(($validos->count() / $totalActivos) * 100, 2) : 0;
        $sindicatos = Sindicato::orderBy('nombre')->get();

        return view('activities.reports.show', compact('activity', 'totalActivos', 'validos', 'revisiones', 'porcentaje', 'sindicatos'));
    }

    public function export(Activity $activity, Request $request)
    {
        $sindicatoId = $request->integer('sindicato_id');
        $rows = $activity->attendances()
            ->with('affiliate.sindicato')
            ->whereNull('reverted_at')
            ->when($sindicatoId, fn ($query) => $query->whereHas('affiliate', fn ($subquery) => $subquery->where('sindicato_id', $sindicatoId)))
            ->get();
        $csv = "estado,ci,nombre,item,sindicato,observacion\n";

        foreach ($rows as $attendance) {
            $csv .= implode(',', [
                $attendance->estado,
                $attendance->ci_detectado,
                '"'.str_replace('"', '""', $attendance->affiliate?->full_name ?? '').'"',
                '"'.str_replace('"', '""', $attendance->affiliate?->item_principal ?? '').'"',
                '"'.str_replace('"', '""', $attendance->affiliate?->sindicato?->nombre ?? '').'"',
                '"'.str_replace('"', '""', $attendance->observacion ?? '').'"',
            ])."\n";
        }

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="asistencia-'.$activity->id.'.csv"',
        ]);
    }

    public function revert(Activity $activity, Request $request): RedirectResponse
    {
        abort_unless(Auth::user()?->role->canModifyCi(), 403, 'Solo Administrador puede revertir importaciones.');

        $batchId = $request->string('batch_id')->toString();
        abort_if($batchId === '', 422, 'Falta el lote de importacion.');

        $affected = $activity->attendances()
            ->where('import_batch_id', $batchId)
            ->whereNull('reverted_at')
            ->update([
                'reverted_at' => now(),
                'reverted_by' => Auth::id(),
                'updated_at' => now(),
            ]);

        AuditLogger::record('asistencia.importacion_revertida', $activity, [], [
            'batch_id' => $batchId,
            'filas_revertidas' => $affected,
        ]);

        return redirect()->route('actividades.asistencias.index', $activity)->with('status', 'Importacion revertida sin borrar registros.');
    }

    private function summary(Activity $activity, int $sindicatoId = 0): array
    {
        $filter = fn ($query) => $query
            ->whereNull('reverted_at')
            ->when($sindicatoId, fn ($subquery) => $subquery->whereHas('affiliate', fn ($affiliateQuery) => $affiliateQuery->where('sindicato_id', $sindicatoId)));

        return [
            'validos' => $filter($activity->attendances())->where('estado', 'valido')->count(),
            'duplicados' => $filter($activity->attendances())->where('estado', 'duplicado')->count(),
            'observados' => $filter($activity->attendances())->where('estado', 'observado')->count(),
            'invalidos' => $filter($activity->attendances())->where('estado', 'invalido')->count(),
        ];
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->role->canManageAffiliates(), 403, 'No tienes permiso para importar asistencia.');
    }
}
