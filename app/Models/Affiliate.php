<?php

namespace App\Models;

use App\Enums\AffiliateStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Affiliate extends Model
{
    use HasFactory;

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

    public function credential(): HasOne
    {
        return $this->hasOne(AffiliateCredential::class);
    }
}
