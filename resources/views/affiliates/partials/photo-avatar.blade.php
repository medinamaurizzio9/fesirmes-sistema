@php
    $sizeClasses = $size ?? 'h-12 w-12';
    $textClasses = $text ?? 'text-sm';
@endphp

<div class="inline-flex flex-col items-center gap-1">
    @if ($affiliate->photo_url)
        <img src="{{ $affiliate->photo_url }}" alt="Fotografia de {{ $affiliate->full_name }}" class="{{ $sizeClasses }} rounded-md object-cover ring-1 ring-slate-200" data-photo-preview>
    @else
        <div class="{{ $sizeClasses }} {{ $textClasses }} flex items-center justify-center rounded-md bg-cyan-50 font-bold text-cyan-800 ring-1 ring-cyan-100" data-photo-placeholder>
            {{ $affiliate->initials() }}
        </div>
        <img src="" alt="Vista previa de fotografia" class="{{ $sizeClasses }} hidden rounded-md object-cover ring-1 ring-slate-200" data-photo-preview>
    @endif
</div>
