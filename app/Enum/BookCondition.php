<?php

namespace App\Enum;

enum BookCondition: string
{
    case Good = 'Good';
    case Fair = 'Fair';
    case Poor = 'Poor';

    public function label(): string
    {
        return match ($this) {
            static::Good => 'Baik',
            static::Fair => 'Layak',
            static::Poor => 'Buruk',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            static::Good => 'primary',
            static::Fair => 'info',
            static::Poor => 'secondary',
        };
    }
}
