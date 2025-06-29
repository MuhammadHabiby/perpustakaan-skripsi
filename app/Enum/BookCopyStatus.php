<?php

namespace App\Enum;

enum BookCopyStatus: string
{
    case Available = 'Available';
    case Borrowed = 'Borrowed';
    case Booked = 'Booked';
    case Lost = 'Lost';
    case Damaged = 'Damaged';
    case Maintenance = 'Maintenance';

    public function label(): string
    {
        return match ($this) {
            static::Available => 'Tersedia',
            static::Borrowed => 'Dipinjam',
            static::Booked => 'Dipesan',
            static::Lost => 'Hilang',
            static::Damaged => 'Rusak',
            static::Maintenance => 'Perawatan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            static::Available => 'success',
            static::Borrowed, static::Booked => 'warning',
            static::Lost, static::Damaged, static::Maintenance => 'danger',
        };
    }

    public function isAvailableForCirculation(): bool
    {
        return $this === static::Available;
    }
}
