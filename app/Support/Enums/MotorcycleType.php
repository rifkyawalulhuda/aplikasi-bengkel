<?php

namespace App\Support\Enums;

enum MotorcycleType: string
{
    case Matic = 'matic';
    case Bebek = 'bebek';
    case Sport = 'sport';
    case Lainnya = 'lainnya';

    public function label(): string
    {
        return match ($this) {
            self::Matic => 'Matic',
            self::Bebek => 'Bebek',
            self::Sport => 'Sport',
            self::Lainnya => 'Lainnya',
        };
    }
}
