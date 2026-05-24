<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Http\Requests\UpdateActivityRequest;
use App\Models\Activity;
use App\Models\Affiliate;
use App\Models\Attendance;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $activities = Activity::query()
            ->when($request->string('buscar')->toString(), fn ($query, string $term) => $query
                ->where(fn ($subquery) => $subquery
                    ->where('nombre', 'like', "%{$term}%")
                    ->orWhere('lugar', 'like', "%{$term}%")))
            ->when($request->string('estado')->toString(), fn ($query, string $estado) => $query->where('estado', $estado))
            ->latest('fecha')
            ->paginate(10)
            ->withQueryString();

        return view('activities.index', compact('activities'));
    }

    public function create(): View
    {
        $this->authorizeManage();

        return view('activities.create', ['activity' => new Activity(['estado' => 'programada'])]);
    }

    public function store(StoreActivityRequest $request): RedirectResponse
    {
        $activity = Activity::create($request->validated() + ['created_by' => Auth::id()]);

        AuditLogger::record('actividad.creada', $activity, [], $activity->toArray());

        return redirect()->route('actividades.show', $activity)->with('status', 'Actividad creada correctamente.');
    }

    public function show(Activity $activity): View
    {
        $activity->loadCount([
            'attendances as valid_attendances_count' => fn ($query) => $query->where('estado', 'valido')->whereNull('reverted_at'),
            'attendances as invalid_attendances_count' => fn ($query) => $query->whereIn('estado', ['invalido', 'observado', 'duplicado'])->whereNull('reverted_at'),
        ]);

        return view('activities.show', compact('activity'));
    }

    public function edit(Activity $activity): View
    {
        $this->authorizeManage();

        return view('activities.edit', compact('activity'));
    }

    public function update(UpdateActivityRequest $request, Activity $activity): RedirectResponse
    {
        $oldValues = $activity->only(array_keys($request->validated()));
        $activity->update($request->validated());

        AuditLogger::record('actividad.actualizada', $activity, $oldValues, $activity->fresh()->only(array_keys($request->validated())));

        return redirect()->route('actividades.show', $activity)->with('status', 'Actividad actualizada correctamente.');
    }

    public function destroy(Activity $activity): RedirectResponse
    {
        abort_unless(Auth::user()?->role->canModifyCi(), 403, 'Solo Administrador puede cancelar actividades.');

        $oldValues = ['estado' => $activity->estado];
        $activity->update(['estado' => 'cancelada']);

        AuditLogger::record('actividad.cancelada', $activity, $oldValues, ['estado' => 'cancelada']);

        return redirect()->route('actividades.index')->with('status', 'Actividad cancelada. No fue eliminada fisicamente.');
    }

    public function generalReport(): View
    {
        $totalRealizadas = Activity::where('estado', 'realizada')->count();
        $affiliates = Affiliate::query()
            ->orderBy('apellido_paterno')
            ->orderBy('nombres')
            ->paginate(25);

        $validCounts = Attendance::where('estado', 'valido')
            ->whereNull('reverted_at')
            ->whereHas('activity', fn ($query) => $query->where('estado', 'realizada'))
            ->selectRaw('affiliate_id, COUNT(*) as total')
            ->groupBy('affiliate_id')
            ->pluck('total', 'affiliate_id');

        return view('activities.reports.general', compact('affiliates', 'totalRealizadas', 'validCounts'));
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->role->canManageAffiliates(), 403, 'No tienes permiso para gestionar actividades.');
    }
}
