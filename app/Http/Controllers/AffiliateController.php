<?php

namespace App\Http\Controllers;

use App\Enums\AffiliateStatus;
use App\Http\Requests\StoreAffiliateRequest;
use App\Http\Requests\UpdateAffiliateRequest;
use App\Models\Affiliate;
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
            ->when($request->string('buscar')->toString(), function ($query, string $term) {
                $query->where(function ($query) use ($term) {
                    $query->where('ci', 'like', "%{$term}%")
                        ->orWhere('nombres', 'like', "%{$term}%")
                        ->orWhere('apellido_paterno', 'like', "%{$term}%")
                        ->orWhere('apellido_materno', 'like', "%{$term}%")
                        ->orWhere('item_principal', 'like', "%{$term}%")
                        ->orWhere('first_name', 'like', "%{$term}%")
                        ->orWhere('last_name', 'like', "%{$term}%");
                });
            })
            ->when($request->string('estado')->toString(), fn ($query, string $status) => $query->where('status', $status))
            ->latest();

        return view('affiliates.index', [
            'affiliates' => $query->paginate(10)->withQueryString(),
            'statuses' => AffiliateStatus::cases(),
        ]);
    }

    public function create(): View
    {
        $this->authorizeManage();

        return view('affiliates.create', [
            'affiliate' => new Affiliate(['status' => AffiliateStatus::Activo]),
            'statuses' => AffiliateStatus::cases(),
        ]);
    }

    public function store(StoreAffiliateRequest $request): RedirectResponse
    {
        $data = $this->affiliateData($request);

        $affiliate = Affiliate::create($data);

        AuditLogger::record('afiliado.creado', $affiliate, [], $affiliate->toArray());

        return redirect()->route('afiliados.show', $affiliate)->with('status', 'Afiliado creado correctamente.');
    }

    public function show(Affiliate $affiliate): View
    {
        return view('affiliates.show', compact('affiliate'));
    }

    public function edit(Affiliate $affiliate): View
    {
        $this->authorizeManage();

        return view('affiliates.edit', [
            'affiliate' => $affiliate,
            'statuses' => AffiliateStatus::cases(),
        ]);
    }

    public function update(UpdateAffiliateRequest $request, Affiliate $affiliate): RedirectResponse
    {
        $data = $this->affiliateData($request, $affiliate);
        $oldValues = $affiliate->only(array_keys($data));

        $affiliate->update($data);

        AuditLogger::record('afiliado.actualizado', $affiliate, $oldValues, $affiliate->fresh()->only(array_keys($data)));

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
