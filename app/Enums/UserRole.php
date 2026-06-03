<?php

namespace App\Enums;

enum UserRole: string
{
    case Administrador = 'Administrador';
    case Secretaria = 'Secretaría';
    case Consulta = 'Consulta';
    case Afiliado = 'Afiliado';

    public function canManageAffiliates(): bool
    {
        return in_array($this, [self::Administrador, self::Secretaria], true);
    }

    public function canModifyCi(): bool
    {
        return $this === self::Administrador;
    }

    public function isAffiliate(): bool
    {
        return $this === self::Afiliado;
    }
}
