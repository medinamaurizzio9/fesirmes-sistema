<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateStatus;
use App\Http\Requests\StoreAffiliateRequest;
use App\Http\Requests\UpdateAffiliateRequest;
use App\Models\Affiliate;
use App\Models\Sindicato;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function index(Request $request): View
    {
        $query = Affiliate::query()
            ->with('sindicato')
            ->when($request->string('buscar')->toString(), function ($query, string $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('ci', 'like', "%{$term}%")
                        ->orWhere('nombres', 'like', "%{$term}%")
                        ->orWhere('apellido_paterno', 'like', "%{$term}%")
                        ->orWhere('apellido_materno', 'like', "%{$term}%")
                        ->orWhere('item_principal', 'like', "%{$term}%")
                        ->orWhere('tipo_item', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%")
                        ->orWhereHas('sindicato', fn ($subquery) => $subquery
                            ->where('nombre', 'like', "%{$term}%")
                            ->orWhere('sigla', 'like', "%{$term}%"));
                });
            })
            ->when($request->string('estado')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->when($request->string('tipo_item')->toString(), fn ($query, string $tipoItem) => $query->where('tipo_item', $tipoItem))
            ->when($request->integer('sindicato_id'), fn ($query, int $sindicatoId) => $query->where('sindicato_id', $sindicatoId))
            ->latest();

        return view('affiliates.index', [
            'affiliates' => $query->paginate(10)->withQueryString(),
            'statuses' => AffiliateStatus::cases(),
            'itemTypes' => Affiliate::itemTypes(),
            'sindicatos' => Sindicato::orderBy('nombre')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeManage();

        return view('affiliates.create', [
            'affiliate' => new Affiliate(['status' => AffiliateStatus::Activo]),
            'statuses' => AffiliateStatus::cases(),
            'itemTypes' => Affiliate::itemTypes(),
            'sindicatos' => Sindicato::where('estado', 'activo')->orderBy('nombre')->get(),
        ]);
    }

    public function store(StoreAffiliateRequest $request): RedirectResponse
    {
        $data = $this->affiliateData($request);

        $affiliate = Affiliate::create($data);

        AuditLogger::record('afiliado.creado', $affiliate, [], $affiliate->toArray());
        AuditLogger::record('afiliado.sindicato_asignado', $affiliate, [], ['sindicato_id' => $affiliate->sindicato_id]);

        return redirect()->route('afiliados.show', $affiliate)->with('status', 'Afiliado creado correctamente.');
    }

    public function show(Affiliate $affiliate): View
    {
        $affiliate->load('sindicato');

        return view('affiliates.show', compact('affiliate'));
    }

    public function edit(Affiliate $affiliate): View
    {
        $this->authorizeManage();

        return view('affiliates.edit', [
            'affiliate' => $affiliate,
            'statuses' => AffiliateStatus::cases(),
            'itemTypes' => Affiliate::itemTypes(),
            'sindicatos' => Sindicato::where('estado', 'activo')
                ->orWhere('id', $affiliate->sindicato_id)
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function update(UpdateAffiliateRequest $request, Affiliate $affiliate): RedirectResponse
    {
        $data = $this->affiliateData($request, $affiliate);
        $oldValues = $affiliate->only(array_keys($data));
        $oldSindicatoId = $affiliate->sindicato_id;

        $affiliate->update($data);

        AuditLogger::record('afiliado.actualizado', $affiliate, $oldValues, $affiliate->fresh()->only(array_keys($data)));

        if (array_key_exists('sindicato_id', $data) && (int) $oldSindicatoId !== (int) $data['sindicato_id']) {
            AuditLogger::record('afiliado.sindicato_actualizado', $affiliate, ['sindicato_id' => $oldSindicatoId], ['sindicato_id' => $data['sindicato_id']]);
        }

        return redirect()->route('afiliados.show', $affiliate)->with('status', 'Afiliado actualizado correctamente.');
    }

    public function photo(Affiliate $affiliate): BinaryFileResponse
    {
        abort_unless($affiliate->photo_path && Storage::disk('local')->exists($affiliate->photo_path), 404);

        return response()->file(Storage::disk('local')->path($affiliate->photo_path), [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    public function destroy(Affiliate $affiliate): RedirectResponse
    {
        abort_unless(auth()->user()?->role->canModifyCi(), 403, 'Solo Administrador puede cambiar el estado del afiliado.');

        $oldValues = ['status' => $affiliate->status->value];
        $affiliate->update(['status' => AffiliateStatus::Baja]);

        AuditLogger::record('afiliado.baja', $affiliate, $oldValues, ['status' => AffiliateStatus::Baja->value]);

        return redirect()->route('afiliados.index')->with('status', 'El afiliado fue marcado como baja. No se elimino fisicamente.');
    }

    private function authorizeManage(): void
    {
        abort_unless(auth()->user()?->role->canManageAffiliates(), 403, 'No tienes permiso para modificar afiliados.');
    }

    private function affiliateData(StoreAffiliateRequest|UpdateAffiliateRequest $request, ?Affiliate $affiliate = null): array
    {
        $data = $request->validated();
        unset($data['photo']);
        $data['sindicato_id'] = $data['sindicato_id'] ?? Sindicato::direct()->id;

        if ($request instanceof StoreAffiliateRequest) {
            $data['status'] = AffiliateStatus::Activo;
        }

        $data['first_name'] = $data['nombres'];
        $data['last_name'] = trim(($data['apellido_paterno'] ?? '').' '.($data['apellido_materno'] ?? ''));
        $data['phone'] = $data['celular'] ?? null;
        $data['address'] = $data['domicilio'] ?? null;
        $data['joined_at'] = $data['fecha_ingreso_sistema'] ?? null;

        $data['formacion_academica'] = collect($data['formacion_academica'] ?? [])
            ->take(3)
            ->map(fn (array $item) => array_filter($item, fn ($value) => filled($value)))
            ->filter()
            ->values()
            ->all();

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $oldPhotoPath = $affiliate?->photo_path;
            $newPhotoPath = $request->file('photo')->store('afiliados/fotografias', 'local');

            if ($oldPhotoPath) {
                Storage::disk('local')->delete($oldPhotoPath);
            }

            $data['photo_path'] = $newPhotoPath;
        }

        return $data;
    }
}
