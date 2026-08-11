<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Impaye = 'impaye';
    case PartiellementPaye = 'partiellement_paye';
    case Paye = 'paye';
    case Rembourse = 'rembourse';

    public function label(): string
    {
        return match ($this) {
            self::Impaye => 'Impayé',
            self::PartiellementPaye => 'Partiellement payé',
            self::Paye => 'Payé',
            self::Rembourse => 'Remboursé',
        };
    }
}
