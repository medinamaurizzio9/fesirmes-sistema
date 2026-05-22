<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SystemLogoController extends Controller
{
    public function edit(): View
    {
        return view('settings.logo', [
            'logoPath' => SystemSetting::getValue('system_logo_path'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $oldPath = SystemSetting::getValue('system_logo_path');
        if ($oldPath) {
            Storage::disk('local')->delete($oldPath);
        }

        $path = $validated['logo']->store('sistema/logo', 'local');
        SystemSetting::setValue('system_logo_path', $path);

        AuditLogger::record('sistema.logo_actualizado', null, [
            'logo_path' => $oldPath,
        ], [
            'logo_path' => $path,
        ]);

        return redirect()->route('settings.logo.edit')->with('status', 'Logo institucional actualizado correctamente.');
    }

    public function show(): BinaryFileResponse|Response
    {
        $path = SystemSetting::getValue('system_logo_path');
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }

    public function downloadPng(): Response
    {
        $path = SystemSetting::getValue('system_logo_path');
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        $png = $this->transparentPng(Storage::disk('local')->path($path));

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
