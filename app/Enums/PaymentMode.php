<?php

namespace App\Enums;

enum PaymentMode: string
{
    case Especes = 'especes';
    case Carte = 'carte';
    case Cheque = 'cheque';
    case Virement = 'virement';
    case MobileMoney = 'mobile_money';

    public function label(): string
    {
        return match ($this) {
            self::Especes => 'Espèces',
            self::Carte => 'Carte',
            self::Cheque => 'Chèque',
            self::Virement => 'Virement',
            self::MobileMoney => 'Mobile money',
        };
    }
}
