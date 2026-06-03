<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 0; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: DejaVu Sans, sans-serif; color: #0f172a; }
        .credential-card { width: 85.6mm; height: 54mm; overflow: hidden; border: 1px solid #cbd5e1; border-radius: 8px; background: #fff; position: relative; }
        .credential-card:before { content: ""; position: absolute; left: 0; top: 0; width: 100%; height: 8px; background: #155e75; }
        .credential-brand { padding: 13px 12px 6px; display: table; width: 100%; }
        .credential-logo { display: table-cell; width: 28px; height: 28px; border-radius: 5px; background: #155e75; color: #fff; text-align: center; vertical-align: middle; font-weight: bold; font-size: 10px; }
        .credential-logo-img { display: table-cell; width: 28px; height: 28px; object-fit: contain; border-radius: 5px; }
        .credential-brand-text { display: table-cell; padding-left: 8px; vertical-align: middle; }
        .credential-title { font-size: 13px; font-weight: bold; letter-spacing: .5px; }
        .credential-subtitle { font-size: 6.8px; color: #64748b; text-transform: uppercase; }
        .credential-body { display: table; width: 100%; padding: 3px 10px 0; table-layout: fixed; }
        .credential-photo, .credential-data, .credential-qr { display: table-cell; vertical-align: top; }
        .credential-photo { width: 21mm; }
        .credential-photo img, .credential-avatar { width: 20mm; height: 24mm; object-fit: cover; border: 1px solid #cbd5e1; border-radius: 5px; }
        .credential-avatar { text-align: center; line-height: 24mm; color: #155e75; background: #ecfeff; font-size: 18px; font-weight: bold; }
        .credential-data { width: 42mm; padding: 0 5px; }
        .credential-label { font-size: 6.3px; color: #64748b; text-transform: uppercase; font-weight: bold; }
        .credential-name { margin-top: 2px; font-size: 10.5px; line-height: 1.15; font-weight: bold; text-transform: uppercase; }
        .credential-value { margin-top: 1px; font-size: 8.5px; line-height: 1.15; font-weight: bold; word-break: break-word; overflow-wrap: anywhere; }
        .credential-grid { margin-top: 4px; }
        .credential-grid div { margin-bottom: 3px; }
        .credential-qr { width: 20mm; text-align: center; }
        .credential-qr img { width: 20mm; height: 20mm; }
        .credential-qr-text { font-size: 6px; color: #64748b; font-weight: bold; margin-top: 1px; }
        .credential-footer { position: absolute; left: 12px; right: 12px; bottom: 7px; border-top: 1px solid #e2e8f0; padding-top: 3px; font-size: 6px; color: #64748b; }
        .credential-footer-left { float: left; }
        .credential-footer-right { float: right; }
    </style>
</head>
<body>
    <div class="credential-card">
        <div class="credential-brand">
            @if ($logoDataUri)
                <img src="{{ $logoDataUri }}" alt="Logo" class="credential-logo-img">
            @else
                <div class="credential-logo">FE</div>
            @endif
            <div class="credential-brand-text">
                <div class="credential-title">FESIRMES</div>
                <div class="credential-subtitle">Credencial Digital Institucional</div>
            </div>
        </div>
        <div class="credential-body">
            <div class="credential-photo">
                @if ($photoDataUri)
                    <img src="{{ $photoDataUri }}" alt="Fotografia">
                @else
                    <div class="credential-avatar">{{ $affiliate->initials() }}</div>
                @endif
            </div>
            <div class="credential-data">
                <div class="credential-label">Nombre completo</div>
                <div class="credential-name">{{ $affiliate->full_name }}</div>
                <div class="credential-grid">
                    <div>
                        <div class="credential-label">C.I.</div>
                        <div class="credential-value">{{ $affiliate->ci }}</div>
                    </div>
                    <div>
                        <div class="credential-label">Item</div>
                        <div class="credential-value">{{ $affiliate->item_principal ?? 'Sin item' }}</div>
                    </div>
                </div>
            </div>
            <div class="credential-qr">
                <img src="{{ $qrDataUri }}" alt="QR">
                <div class="credential-qr-text">QR C.I.</div>
            </div>
        </div>
        <div class="credential-footer">
            <span class="credential-footer-left">Uso institucional</span>
            <span class="credential-footer-right">Version QR {{ $credential->qr_version }}</span>
        </div>
    </div>
</body>
</html>
