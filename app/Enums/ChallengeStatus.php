<?php

namespace App\Enums;

enum ChallengeStatus: string
{
    case Planifie = 'planifie';
    case EnCours = 'en_cours';
    case Termine = 'termine';
    case Suspendu = 'suspendu';
    case Annule = 'annule';

    public function label(): string
    {
        return match ($this) {
            self::Planifie => 'Planifié',
            self::EnCours => 'En cours',
            self::Termine => 'Terminé',
            self::Suspendu => 'Suspendu',
            self::Annule => 'Annulé',
        };
    }
}
