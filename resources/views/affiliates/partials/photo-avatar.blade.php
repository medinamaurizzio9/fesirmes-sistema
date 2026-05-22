@php
    $sizeClasses = $size ?? 'h-12 w-12';
    $textClasses = $text ?? 'text-sm';
    $initials = mb_strtoupper(mb_substr($affiliate->nombres ?? $affiliate->first_name ?? 'A', 0, 1).mb_substr($affiliate->apellido_paterno ?? $affiliate->last_name ?? 'F', 0, 1));
@endphp

@if ($affiliate->photo_path)
    <img src="{{ route('afiliados.photo', $affiliate) }}" alt="Fotografia de {{ $affiliate->full_name }}" class="{{ $sizeClasses }} rounded-md object-cover ring-1 ring-slate-200">
@else
    <div class="{{ $sizeClasses }} {{ $textClasses }} flex items-center justify-center rounded-md bg-cyan-50 font-bold text-cyan-800 ring-1 ring-cyan-100">
        {{ $initials }}
    </div>
@endif
