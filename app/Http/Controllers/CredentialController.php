<?php

namespace App\Http\Controllers;

use App\Models\Affiliate;
use App\Models\AffiliateCredential;
use App\Models\SystemSetting;
use App\Services\AuditLogger;
use App\Services\CredentialQrCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CredentialController extends Controller
{
    public function show(Affiliate $affiliate, CredentialQrCode $qrCode): View
    {
        $credential = $this->ensureCredential($affiliate);

        AuditLogger::record('credencial.visualizada', $affiliate, [], [
            'qr_payload' => $credential->qr_payload,
        ]);

        return view('credentials.show', [
            'affiliate' => $affiliate,
            'credential' => $credential,
            'qrDataUri' => $qrCode->dataUri($credential->qr_payload),
            'photoDataUri' => $this->photoDataUri($affiliate),
            'logoDataUri' => $this->systemLogoDataUri(),
            'institution' => SystemSetting::institutional(),
        ]);
    }

    public function showMine(CredentialQrCode $qrCode): View|RedirectResponse
    {
        $affiliate = auth()->user()?->affiliate;
        abort_unless($affiliate, 403);

        if ($affiliate->hasRestrictedPortalAccess()) {
            AuditLogger::record('afiliado.acceso_restringido', $affiliate, [], [
                'estado' => $affiliate->portalStatusValue(),
                'fecha' => now()->toDateTimeString(),
                'ip' => request()->ip(),
                'intento' => 'credencial',
            ]);

            return redirect()->route('affiliate.profile')
                ->with('status', 'Su registro se encuentra restringido. Mientras mantenga este estado no podrá realizar modificaciones ni acceder a su credencial.');
        }

        return $this->show($affiliate, $qrCode);
    }

    public function pdf(Affiliate $affiliate, CredentialQrCode $qrCode): Response
    {
        $credential = $this->ensureCredential($affiliate);

        AuditLogger::record('credencial.pdf_descargado', $affiliate, [], [
            'qr_payload' => $credential->qr_payload,
        ]);

        $pdf = Pdf::loadView('credentials.pdf', [
            'affiliate' => $affiliate,
            'credential' => $credential,
            'qrDataUri' => $qrCode->dataUri($credential->qr_payload, 260),
            'photoDataUri' => $this->photoDataUri($affiliate),
            'logoDataUri' => $this->systemLogoDataUri(),
            'institution' => SystemSetting::institutional(),
        ])->setPaper([0, 0, 242.65, 153.07], 'landscape');

        return $pdf->download('credencial-'.$affiliate->ci.'.pdf');
    }

    public function print(Affiliate $affiliate, CredentialQrCode $qrCode): View
    {
        $credential = $this->ensureCredential($affiliate);

        AuditLogger::record('credencial.visualizada_impresion', $affiliate, [], [
            'qr_payload' => $credential->qr_payload,
        ]);

        return view('credentials.print', [
            'affiliate' => $affiliate,
            'credential' => $credential,
            'qrDataUri' => $qrCode->dataUri($credential->qr_payload),
            'photoDataUri' => $this->photoDataUri($affiliate),
            'logoDataUri' => $this->systemLogoDataUri(),
            'institution' => SystemSetting::institutional(),
        ]);
    }

    public function auditPng(Affiliate $affiliate): Response
    {
        $credential = $this->ensureCredential($affiliate);

        AuditLogger::record('credencial.png_descargado', $affiliate, [], [
            'qr_payload' => $credential->qr_payload,
        ]);

        return response()->noContent();
    }

    public function regenerate(Affiliate $affiliate): RedirectResponse
    {
        abort_unless(Auth::user()?->role->canModifyCi(), 403, 'Solo Administrador puede regenerar QR.');

        $credential = $this->ensureCredential($affiliate, true);

        AuditLogger::record('credencial.qr_regenerado', $affiliate, [], [
            'qr_payload' => $credential->qr_payload,
            'qr_version' => $credential->qr_version,
        ]);

        return redirect()->route('afiliados.credential.show', $affiliate)
            ->with('status', 'QR regenerado correctamente.');
    }

    private function ensureCredential(Affiliate $affiliate, bool $force = false): AffiliateCredential
    {
        $credential = $affiliate->credential()->firstOrNew([
            'affiliate_id' => $affiliate->id,
        ]);

        $mustSync = ! $credential->exists || $credential->qr_payload !== $affiliate->ci || $force;

        if ($mustSync) {
            $oldPayload = $credential->qr_payload;
            $credential->qr_payload = $affiliate->ci;
            $credential->qr_version = $credential->exists ? $credential->qr_version + 1 : 1;
            $credential->regenerated_by = Auth::id();
            $credential->regenerated_at = now();
            $credential->save();

            if ($oldPayload && $oldPayload !== $credential->qr_payload && ! $force) {
                AuditLogger::record('credencial.qr_regenerado', $affiliate, [
                    'qr_payload' => $oldPayload,
                ], [
                    'qr_payload' => $credential->qr_payload,
                    'qr_version' => $credential->qr_version,
                ]);
            }
        }

        return $credential;
    }

    private function photoDataUri(Affiliate $affiliate): ?string
    {
        if (! $affiliate->hasPhoto()) {
            return null;
        }

        $path = Storage::disk('public')->path($affiliate->photo_path);
        $mime = mime_content_type($path) ?: 'image/jpeg';

        return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($path));
    }

    private function systemLogoDataUri(): ?string
    {
        return SystemSetting::logoDataUri();
    }
}
