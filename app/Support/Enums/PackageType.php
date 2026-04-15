<?php

namespace App\Support\Enums;

enum PackageType: string
{
    case FixedPackage = 'fixed_package';
    case CustomPackage = 'custom_package';

    public function label(): string
    {
        return match ($this) {
            self::FixedPackage => 'Paket Tetap',
            self::CustomPackage => 'Paket Custom',
        };
    }
}
