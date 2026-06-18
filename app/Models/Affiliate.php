<?php

namespace App\Models;

use App\Enums\AffiliateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Affiliate extends Model
{
    use HasFactory;

    public const ITEM_TYPES = [
        'SEDES',
        'MINISTERIAL',
        'INLASA',
        'SEDEGES',
    ];

    public const RESTRICTED_PORTAL_STATUSES = [
        'baja',
        'suspendido',
        'observado',
        'inactivo',
    ];

    public const PROFESSIONAL_TITLES = [
        'LIC.',
        'DR.',
        'DRA.',
    ];

    protected $fillable = [
        'ci',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'lugar_fecha_nacimiento',
        'nacionalidad',
        'domicilio',
        'celular',
        'first_name',
        'last_name',
        'phone',
        'email',
        'telefono',
        'idioma_castellano',
        'idioma_ingles',
        'idioma_aymara',
        'idioma_quechua',
        'idioma_otros',
        'formacion_academica',
        'diplomado_universidad',
        'diplomado_anio',
        'diplomado_titulo',
        'especialidad_universidad',
        'especialidad_anio',
        'especialidad_titulo',
        'maestria_universidad',
        'maestria_anio',
        'maestria_titulo',
        'lugar_trabajo',
        'red_salud',
        'item_principal',
        'item_secundario',
        'tipo_item',
        'sindicato_id',
        'fecha_ingreso_sistema',
        'fecha_primer_descuento_fesirmes',
        'tematica_capacitacion',
        'deportes',
        'photo_path',
        'address',
        'birth_date',
        'joined_at',
        'status',
        'professional_title',
        'is_directorio',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'joined_at' => 'date',
            'idioma_castellano' => 'boolean',
            'idioma_ingles' => 'boolean',
            'idioma_aymara' => 'boolean',
            'idioma_quechua' => 'boolean',
            'formacion_academica' => 'array',
            'fecha_ingreso_sistema' => 'date',
            'fecha_primer_descuento_fesirmes' => 'date',
            'status' => AffiliateStatus::class,
            'is_directorio' => 'boolean',
        ];
    }

    public function getFullNameAttribute(): string
    {
        $newName = trim(collect([
            $this->nombres,
            $this->apellido_paterno,
            $this->apellido_materno,
        ])->filter()->implode(' '));

        return $newName !== '' ? $newName : trim($this->first_name.' '.$this->last_name);
    }

    public function getFullNameWithTitleAttribute(): string
    {
        return trim(collect([$this->professional_title, $this->full_name])->filter()->implode(' '));
    }

    public function initials(): string
    {
        $first = $this->nombres ?: $this->first_name ?: $this->full_name ?: 'A';
        $last = $this->apellido_paterno ?: $this->last_name ?: $this->apellido_materno ?: 'F';

        return mb_strtoupper(mb_substr($first, 0, 1).mb_substr($last, 0, 1));
    }

    public function hasPhoto(): bool
    {
        $path = $this->normalizedPhotoPath();

        return filled($path) && (
            Storage::disk('public')->exists($path)
            || Storage::disk('local')->exists($path)
        );
    }

    public function getPhotoUrlAttribute(): ?string
    {
        $path = $this->normalizedPhotoPath();

        if (! $path) {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path).'?v='.Storage::disk('public')->lastModified($path);
        }

        if (Storage::disk('local')->exists($path)) {
            $absolutePath = Storage::disk('local')->path($path);
            $mime = mime_content_type($absolutePath) ?: 'image/jpeg';

            return 'data:'.$mime.';base64,'.base64_encode(file_get_contents($absolutePath));
        }

        return null;
    }

    public function photoUrl(): ?string
    {
        return $this->photo_url;
    }

    public function photoAbsolutePath(): ?string
    {
        $path = $this->normalizedPhotoPath();

        if (! $path) {
            return null;
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->path($path);
        }

        if (Storage::disk('local')->exists($path)) {
            return Storage::disk('local')->path($path);
        }

        return null;
    }

    public function normalizedPhotoPath(): ?string
    {
        return self::normalizeStoragePath($this->photo_path);
    }

    public static function normalizeStoragePath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        $path = str_replace('\\', '/', ltrim($path, '/'));
        $path = preg_replace('#^(storage/app/(public|private)|app/(public|private)|storage|public|private)/#', '', $path);

        return $path ?: null;
    }

    public function portalStatusValue(): string
    {
        return strtolower((string) ($this->getRawOriginal('status') ?? $this->status?->value ?? ''));
    }

    public function hasRestrictedPortalAccess(): bool
    {
        return in_array($this->portalStatusValue(), self::RESTRICTED_PORTAL_STATUSES, true);
    }

    public function portalStatusLabel(): string
    {
        return mb_strtoupper($this->portalStatusValue() ?: 'sin estado');
    }

    public static function itemTypes(): array
    {
        return self::ITEM_TYPES;
    }

    public static function professionalTitles(): array
    {
        return self::PROFESSIONAL_TITLES;
    }

    public function credential(): HasOne
    {
        return $this->hasOne(AffiliateCredential::class);
    }

    public function sindicato(): BelongsTo
    {
        return $this->belongsTo(Sindicato::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
