<?php

namespace App\Enum;

enum BookingStatus: string
{
    case Active = 'Active';
    case Expired = 'Expired';
    case ConvertedToLoan = 'ConvertedToLoan';
    case Cancelled = 'Cancelled';

    public function label(): string
    {
        return match ($this) {
            static::Active => 'Aktif (Menunggu Pengambilan)',
            static::Expired => 'Kadaluarsa',
            static::ConvertedToLoan => 'Sudah Dipinjam',
            static::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            static::Active => 'primary',
            static::Expired => 'secondary',
            static::ConvertedToLoan => 'success',
            static::Cancelled => 'danger',
        };
    }

    public function canBeConverted(): bool
    {
        return $this === static::Active;
    }
}
