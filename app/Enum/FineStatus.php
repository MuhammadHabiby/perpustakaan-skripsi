<?php

namespace App\Enum;

enum FineStatus: string
{
    case Unpaid = 'Unpaid';
    case Paid = 'Paid';
    case Waived = 'Waived';

    public function label(): string
    {
        return match ($this) {
            static::Unpaid => 'Belum Dibayar',
            static::Paid => 'Lunas',
            static::Waived => 'Dibebaskan',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            static::Unpaid => 'danger',
            static::Paid => 'success',
            static::Waived => 'secondary',
        };
    }

    public function isSettled(): bool
    {
        return in_array($this, [static::Paid, static::Waived]);
    }
}
