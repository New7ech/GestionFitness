<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case Presente = 'presente';
    case Absente = 'absente';

    public function label(): string
    {
        return match ($this) {
            self::Presente => 'Présente',
            self::Absente => 'Absente',
        };
    }
}
