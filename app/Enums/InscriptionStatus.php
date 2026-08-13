<?php

namespace App\Enums;

enum InscriptionStatus: string
{
    case Reservee = 'reservee';
    case Terminee = 'terminee';
    case Annulee = 'annulee';

    public function label(): string
    {
        return match ($this) {
            self::Reservee => 'Réservée',
            self::Terminee => 'Terminée',
            self::Annulee => 'Annulée',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Reservee;
    }
}
