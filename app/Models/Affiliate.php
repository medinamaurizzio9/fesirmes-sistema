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

    public function initials(): string
    {
        $first = $this->nombres ?: $this->first_name ?: $this->full_name ?: 'A';
        $last = $this->apellido_paterno ?: $this->last_name ?: $this->apellido_materno ?: 'F';

        return mb_strtoupper(mb_substr($first, 0, 1).mb_substr($last, 0, 1));
    }

    public function hasPhoto(): bool
    {
        return filled($this->photo_path) && Storage::disk('public')->exists($this->photo_path);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        if (! $this->hasPhoto()) {
            return null;
        }

        return Storage::disk('public')->url($this->photo_path);
    }

    public function photoUrl(): ?string
    {
        return $this->photo_url;
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
