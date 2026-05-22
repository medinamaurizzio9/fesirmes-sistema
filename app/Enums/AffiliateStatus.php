<?php

namespace App\Enums;

enum AffiliateStatus: string
{
    case Activo = 'activo';
    case Baja = 'baja';
    case Suspendido = 'suspendido';
    case Observado = 'observado';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
