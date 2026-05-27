<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateStatus;
use App\Http\Requests\StoreSindicatoRequest;
use App\Http\Requests\UpdateSindicatoRequest;
use App\Models\Activity;
use App\Models\Attendance;
use App\Models\Sindicato;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SindicatoController extends Controller
{
    public function index(Request $request): View
    {
        $sindicatos = Sindicato::query()
            ->withCount('affiliates')
            ->when($request->string('buscar')->toString(), fn ($query, string $term) => $query
                ->where(fn ($subquery) => $subquery
                    ->where('nombre', 'like', "%{$term}%")
                    ->orWhere('sigla', 'like', "%{$term}%")))
            ->when($request->string('estado')->toString(), fn ($query, string $estado) => $query->where('estado', $estado))
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('sindicatos.index', [
            'sindicatos' => $sindicatos,
            'statuses' => Sindicato::statuses(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        return view('sindicatos.create', [
            'sindicato' => new Sindicato(['estado' => 'activo']),
            'statuses' => Sindicato::statuses(),
        ]);
    }

    public function store(StoreSindicatoRequest $request): RedirectResponse
    {
        $sindicato = Sindicato::create($request->validated() + ['created_by' => Auth::id()]);

        AuditLogger::record('sindicato.creado', $sindicato, [], $sindicato->toArray());

        return redirect()->route('sindicatos.show', $sindicato)->with('status', 'Sindicato creado correctamente.');
    }

    public function show(Sindicato $sindicato): View
    {
        $statusCounts = collect(AffiliateStatus::cases())
            ->mapWithKeys(fn (AffiliateStatus $status) => [
                $status->value => $sindicato->affiliates()->where('status', $status->value)->count(),
            ]);

        return view('sindicatos.show', [
            'sindicato' => $sindicato,
            'statusCounts' => $statusCounts,
            'affiliates' => $sindicato->affiliates()->orderBy('apellido_paterno')->orderBy('nombres')->paginate(10),
        ]);
    }

    public function edit(Sindicato $sindicato): View
    {
        $this->authorizeAdmin();

        return view('sindicatos.edit', [
            'sindicato' => $sindicato,
            'statuses' => Sindicato::statuses(),
        ]);
    }

    public function update(UpdateSindicatoRequest $request, Sindicato $sindicato): RedirectResponse
    {
        $validated = $request->validated();

        abort_if(
            $sindicato->nombre === Sindicato::DIRECT_NAME && $validated['nombre'] !== Sindicato::DIRECT_NAME,
            422,
            'El registro directo de FESIRMES no puede cambiar de nombre.'
        );

        $oldValues = $sindicato->only(array_keys($validated));
        $sindicato->update($validated);

        AuditLogger::record('sindicato.actualizado', $sindicato, $oldValues, $sindicato->fresh()->only(array_keys($validated)));

        return redirect()->route('sindicatos.show', $sindicato)->with('status', 'Sindicato actualizado correctamente.');
    }

    public function destroy(Sindicato $sindicato): RedirectResponse
    {
        $this->authorizeAdmin();
        abort_if($sindicato->nombre === Sindicato::DIRECT_NAME, 422, 'El registro directo de FESIRMES no puede inactivarse.');

        $oldValues = ['estado' => $sindicato->estado];
        $sindicato->update(['estado' => 'inactivo']);

        AuditLogger::record('sindicato.estado_actualizado', $sindicato, $oldValues, ['estado' => 'inactivo']);

        return redirect()->route('sindicatos.index')->with('status', 'Sindicato marcado como inactivo.');
    }

    public function activate(Sindicato $sindicato): RedirectResponse
    {
        $this->authorizeAdmin();

        $oldValues = ['estado' => $sindicato->estado];
        $sindicato->update(['estado' => 'activo']);

        AuditLogger::record('sindicato.estado_actualizado', $sindicato, $oldValues, ['estado' => 'activo']);

        return redirect()->route('sindicatos.show', $sindicato)->with('status', 'Sindicato activado correctamente.');
    }

    public function report(): View
    {
        $sindicatos = Sindicato::query()
            ->withCount([
                'affiliates as total_afiliados',
                'affiliates as activos' => fn ($query) => $query->where('status', 'activo'),
                'affiliates as bajas' => fn ($query) => $query->where('status', 'baja'),
                'affiliates as suspendidos' => fn ($query) => $query->where('status', 'suspendido'),
                'affiliates as observados' => fn ($query) => $query->where('status', 'observado'),
            ])
            ->orderBy('nombre')
            ->get();

        return view('sindicatos.reports.general', compact('sindicatos'));
    }

    public function attendanceReport(Request $request): View
    {
        $activity = Activity::find($request->integer('activity_id'));
        $activities = Activity::where('estado', 'realizada')->latest('fecha')->get();
        $validCounts = collect();

        if ($activity) {
            $validCounts = Attendance::query()
                ->where('activity_id', $activity->id)
                ->where('estado', 'valido')
                ->whereNull('reverted_at')
                ->whereHas('affiliate')
                ->with('affiliate')
                ->get()
                ->groupBy(fn (Attendance $attendance) => $attendance->affiliate?->sindicato_id);
        }

        $sindicatos = Sindicato::query()
            ->withCount(['affiliates as activos' => fn ($query) => $query->where('status', 'activo')])
            ->orderBy('nombre')
            ->get();

        return view('sindicatos.reports.attendance', compact('sindicatos', 'activities', 'activity', 'validCounts'));
    }

    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->role->canModifyCi(), 403, 'Solo Administrador puede gestionar sindicatos.');
    }
}
