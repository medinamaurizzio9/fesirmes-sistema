<div class="credential-card" data-credential-card>
    @if ($affiliate->is_directorio)
        <div class="credential-directorio">DIRECTORIO</div>
    @endif

    <div class="credential-brand">
        @if ($institution['system_logo_url'] ?? null)
            <img src="{{ $institution['system_logo_url'] }}" alt="Logo FESIRMES" class="credential-logo-img">
        @else
            <div class="credential-logo">FE</div>
        @endif
        <div>
            <div class="credential-title">{{ $institution['institution_acronym'] ?? 'FESIRMES' }}</div>
            <div class="credential-subtitle">{{ $institution['institution_subtitle'] ?? 'Credencial Digital Institucional' }}</div>
        </div>
    </div>

    <div class="credential-body">
        <div class="credential-photo">
            @if ($affiliate->photo_url)
                <img src="{{ $affiliate->photo_url }}" alt="Fotografia de {{ $affiliate->full_name }}">
            @else
                <div class="credential-avatar">{{ $affiliate->initials() }}</div>
            @endif
        </div>

        <div class="credential-data">
            <div class="credential-label">Nombre completo</div>
            <div class="credential-name">{{ $affiliate->full_name_with_title }}</div>

            <div class="credential-grid">
                <div>
                    <div class="credential-label">C.I.</div>
                    <div class="credential-value">{{ $affiliate->ci }}</div>
                </div>
                <div>
                    <div class="credential-label">Item</div>
                    <div class="credential-value credential-item-value">{{ $affiliate->item_principal ?? 'Sin item' }}</div>
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
