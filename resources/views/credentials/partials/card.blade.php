<div class="credential-card" data-credential-card>
    <div class="credential-brand">
        @if ($logoDataUri)
            <img src="{{ $logoDataUri }}" alt="Logo FESIRMES" class="credential-logo-img">
        @else
            <div class="credential-logo">FE</div>
        @endif
        <div>
            <div class="credential-title">FESIRMES</div>
            <div class="credential-subtitle">Credencial Digital Institucional</div>
        </div>
    </div>

    <div class="credential-body">
        <div class="credential-photo">
            @if ($photoDataUri)
                <img src="{{ $photoDataUri }}" alt="Fotografia de {{ $affiliate->full_name }}">
            @elseif ($affiliate->photo_path)
                <img src="{{ route('afiliados.photo', ['affiliate' => $affiliate, 'v' => $affiliate->updated_at?->timestamp, 'p' => md5($affiliate->photo_path)]) }}" alt="Fotografia de {{ $affiliate->full_name }}">
            @else
                <div class="credential-avatar">{{ mb_strtoupper(mb_substr($affiliate->full_name, 0, 2)) }}</div>
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
            <img src="{{ $qrDataUri }}" alt="QR con C.I. {{ $affiliate->ci }}">
            <div class="credential-qr-text">QR C.I.</div>
        </div>
    </div>

    <div class="credential-footer">
        <span>Uso institucional</span>
        <span>Version QR {{ $credential->qr_version }}</span>
    </div>
</div>
