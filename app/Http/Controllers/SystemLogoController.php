<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SystemLogoController extends Controller
{
    public function edit(): View
    {
        return view('settings.logo', [
            'logoPath' => SystemSetting::logoPath(),
            'logoUrl' => SystemSetting::logoUrl(),
            'settings' => SystemSetting::institutional(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'institution_name' => ['required', 'string', 'max:255'],
            'institution_acronym' => ['required', 'string', 'max:50'],
            'institution_subtitle' => ['nullable', 'string', 'max:255'],
            'institution_address' => ['nullable', 'string', 'max:255'],
            'institution_phones' => ['nullable', 'string', 'max:255'],
            'institution_email' => ['nullable', 'email', 'max:255'],
            'institution_website' => ['nullable', 'string', 'max:255'],
            'pdf_footer' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $oldValues = SystemSetting::institutional();
        foreach (array_keys(SystemSetting::DEFAULTS) as $key) {
            SystemSetting::setValue($key, $validated[$key] ?? null);
        }

        $oldPath = SystemSetting::logoPath();
        if ($request->hasFile('logo') && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        $path = $oldPath;
        if ($request->hasFile('logo')) {
            $path = $validated['logo']->store('sistema/logo', 'public');
            SystemSetting::setValue('system_logo_path', $path);
        }

        AuditLogger::record('sistema.configuracion_actualizada', null, $oldValues, SystemSetting::institutional());

        return redirect()->route('settings.logo.edit')->with('status', 'Configuracion institucional actualizada correctamente.');
    }

    public function downloadPng(): Response
    {
        $path = SystemSetting::logoPath();
        abort_unless($path && Storage::disk('public')->exists($path), 404);

        $png = $this->transparentPng(Storage::disk('public')->path($path));

        return response($png, 200, [
            'Content-Type' => 'image/png',
            'Content-Disposition' => 'attachment; filename="logo-fesirmes.png"',
        ]);
    }

    private function transparentPng(string $path): string
    {
        $source = imagecreatefromstring(file_get_contents($path));
        abort_unless($source, 422, 'No se pudo procesar el logo.');

        imagepalettetotruecolor($source);
        imagealphablending($source, true);
        imagesavealpha($source, true);

        ob_start();
        imagepng($source);
        $png = ob_get_clean();
        imagedestroy($source);

        return $png ?: '';
    }
}
