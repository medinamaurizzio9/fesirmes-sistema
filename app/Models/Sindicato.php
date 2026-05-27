<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sindicato extends Model
{
    public const DIRECT_NAME = 'SIN SINDICATO FORMADO / AFILIADO DIRECTO FESIRMES';

    public const STATUSES = [
        'activo',
        'inactivo',
    ];

    protected $fillable = [
        'nombre',
        'sigla',
        'estado',
        'observaciones',
        'created_by',
    ];

    public static function statuses(): array
    {
        return self::STATUSES;
    }

    public static function direct(): self
    {
        return self::firstOrCreate(
            ['nombre' => self::DIRECT_NAME],
            [
                'sigla' => 'FESIRMES',
                'estado' => 'activo',
                'observaciones' => 'Registro por defecto para afiliados directos de FESIRMES.',
            ]
        );
    }

    public function affiliates(): HasMany
    {
        return $this->hasMany(Affiliate::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
