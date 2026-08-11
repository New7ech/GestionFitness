<?php

namespace App\Enums;

enum MeasurementStage: string
{
    case Initiale = 'initiale';
    case Intermediaire = 'intermediaire';
    case Finale = 'finale';

    public function label(): string
    {
        return match ($this) {
            self::Initiale => 'Initiale',
            self::Intermediaire => 'Intermédiaire',
            self::Finale => 'Finale',
        };
    }
}
