<?php

namespace App\Enums;

enum PaymentType: string
{
    case Paiement = 'paiement';
    case Remboursement = 'remboursement';

    public function label(): string
    {
        return match ($this) {
            self::Paiement => 'Paiement',
            self::Remboursement => 'Remboursement',
        };
    }
}
