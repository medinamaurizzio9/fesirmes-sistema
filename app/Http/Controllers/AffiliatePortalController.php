<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\Sindicato;
use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AffiliatePortalController extends Controller
{
    public function profile(): View
    {
        $affiliate = $this->affiliate();
        if ($affiliate->hasRestrictedPortalAccess()) {
            AuditLogger::record('afiliado.acceso_restringido', $affiliate, [], [
                'estado' => $affiliate->portalStatusValue(),
                'fecha' => now()->toDateTimeString(),
                'ip' => request()->ip(),
                'intento' => 'perfil_lectura',
            ]);
        }

        return view('affiliate-portal.profile', [
            'affiliate' => $affiliate,
            'isRestricted' => $affiliate->hasRestrictedPortalAccess(),
            'institution' => SystemSetting::institutional(),
            'academicRows' => array_pad(array_slice($affiliate->formacion_academica ?: [], 0, 3), 3, []),
            'itemTypes' => Affiliate::itemTypes(),
            'sindicatos' => Sindicato::where('estado', 'activo')
                ->orWhere('id', $affiliate->sindicato_id)
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $affiliate = $this->affiliate();
        if ($redirect = $this->restrictedRedirect($affiliate)) {
            return $redirect;
        }

        $validated = $request->validate([
            'lugar_fecha_nacimiento' => ['nullable', 'string', 'max:255'],
            'nacionalidad' => ['nullable', 'string', 'max:100'],
            'celular' => ['nullable', 'string', 'max:30'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'domicilio' => ['nullable', 'string', 'max:255'],
            'idioma_castellano' => ['nullable', 'boolean'],
            'idioma_ingles' => ['nullable', 'boolean'],
            'idioma_aymara' => ['nullable', 'boolean'],
            'idioma_quechua' => ['nullable', 'boolean'],
            'idioma_otros' => ['nullable', 'string', 'max:255'],
            'formacion_academica' => ['nullable', 'array', 'max:3'],
            'formacion_academica.*.carrera' => ['nullable', 'string', 'max:255'],
            'formacion_academica.*.universidad' => ['nullable', 'string', 'max:255'],
            'formacion_academica.*.titulo_academico_numero' => ['nullable', 'string', 'max:100'],
            'formacion_academica.*.titulo_academico_fecha' => ['nullable', 'date'],
            'formacion_academica.*.titulo_provision_nacional_numero' => ['nullable', 'string', 'max:100'],
            'formacion_academica.*.titulo_provision_nacional_fecha' => ['nullable', 'date'],
            'diplomado_universidad' => ['nullable', 'string', 'max:255'],
            'diplomado_anio' => ['nullable', 'string', 'max:10'],
            'diplomado_titulo' => ['nullable', 'string', 'max:255'],
            'especialidad_universidad' => ['nullable', 'string', 'max:255'],
            'especialidad_anio' => ['nullable', 'string', 'max:10'],
            'especialidad_titulo' => ['nullable', 'string', 'max:255'],
            'maestria_universidad' => ['nullable', 'string', 'max:255'],
            'maestria_anio' => ['nullable', 'string', 'max:10'],
            'maestria_titulo' => ['nullable', 'string', 'max:255'],
            'tipo_item' => ['nullable', Rule::in(Affiliate::itemTypes())],
            'sindicato_id' => ['nullable', 'exists:sindicatos,id'],
            'lugar_trabajo' => ['nullable', 'string', 'max:255'],
            'red_salud' => ['nullable', 'string', 'max:255'],
            'fecha_ingreso_sistema' => ['nullable', 'date'],
            'fecha_primer_descuento_fesirmes' => ['nullable', 'date'],
            'deportes' => ['nullable', 'string', 'max:1000'],
            'tematica_capacitacion' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        foreach (['idioma_castellano', 'idioma_ingles', 'idioma_aymara', 'idioma_quechua'] as $field) {
            $validated[$field] = $request->boolean($field);
        }

        $oldValues = $affiliate->only(array_keys($validated));
        unset($validated['photo']);
        unset($validated['status']);
        $validated['sindicato_id'] = $validated['sindicato_id'] ?? Sindicato::direct()->id;
        $validated['phone'] = $validated['celular'] ?? null;
        $validated['address'] = $validated['domicilio'] ?? null;
        $validated['joined_at'] = $validated['fecha_ingreso_sistema'] ?? null;
        $validated['formacion_academica'] = collect($validated['formacion_academica'] ?? [])
            ->take(3)
            ->map(fn (array $item) => array_filter($item, fn ($value) => filled($value)))
            ->filter()
            ->values()
            ->all();

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $oldPhoto = $affiliate->photo_path;
            $validated['photo_path'] = $request->file('photo')->store('afiliados/fotografias', 'public');
            if ($oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }
        }

        $affiliate->update($validated);

        AuditLogger::record('portal_afiliado.perfil_actualizado', $affiliate, $oldValues, $affiliate->fresh()->only(array_keys($validated)));

        return redirect()->route('affiliate.profile')->with('status', 'Perfil actualizado correctamente.');
    }

    public function password(): View|RedirectResponse
    {
        $affiliate = $this->affiliate();
        if ($redirect = $this->restrictedRedirect($affiliate)) {
            return $redirect;
        }

        return view('affiliate-portal.password', [
            'affiliate' => $affiliate,
        ]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();
        $affiliate = $this->affiliate();
        if ($redirect = $this->restrictedRedirect($affiliate)) {
            return $redirect;
        }

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        abort_if($validated['password'] === $affiliate->ci, 422, 'La nueva contraseña no puede ser igual al C.I.');

        $user->update([
            'password' => Hash::make($validated['password']),
            'must_change_password' => false,
            'password_changed_at' => now(),
        ]);

        AuditLogger::record('portal_afiliado.password_actualizado', $affiliate);

        return redirect()->route('affiliate.profile')->with('status', 'Contraseña actualizada correctamente.');
    }

    private function affiliate(): Affiliate
    {
        $affiliate = auth()->user()?->affiliate;
        abort_unless($affiliate, 403, 'No existe afiliado asociado a este usuario.');

        return $affiliate;
    }

    private function restrictedRedirect(Affiliate $affiliate): ?RedirectResponse
    {
        if (! $affiliate->hasRestrictedPortalAccess()) {
            return null;
        }

        AuditLogger::record('afiliado.acceso_restringido', $affiliate, [], [
            'estado' => $affiliate->portalStatusValue(),
            'fecha' => now()->toDateTimeString(),
            'ip' => request()->ip(),
        ]);

        return redirect()->route('affiliate.profile')
            ->with('status', 'Su registro se encuentra restringido. Mientras mantenga este estado no podrá realizar modificaciones ni acceder a su credencial.');
    }
}
