<?php

namespace App\Enum;

enum BorrowingStatus: string
{
    case Borrowed = 'Borrowed';
    case Returned = 'Returned';
    case Overdue = 'Overdue';
    case Lost = 'Lost';

    public function label(): string
    {
        return match ($this) {
            static::Borrowed => 'Dipinjam',
            static::Returned => 'Dikembalikan',
            static::Overdue => 'Lewat Tempo',
            static::Lost => 'Hilang',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            static::Borrowed => 'info',
            static::Returned => 'success',
            static::Overdue => 'warning',
            static::Lost => 'danger',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [static::Borrowed, static::Overdue]);
    }

    public function isOverdue(): bool
    {
        return $this === static::Overdue;
    }

    public function isFinished(): bool
    {
        return in_array($this, [static::Returned, static::Lost]);
    }
}
