<?php

namespace App\Enum;

enum LostReportStatus: string
{
    case Reported = 'Reported';
    case Verified = 'Verified';
    case Resolved = 'Resolved';

    public function label(): string
    {
        return match ($this) {
            static::Reported => 'Dilaporkan',
            static::Verified => 'Terverifikasi',
            static::Resolved => 'Selesai',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            static::Reported => 'warning',
            static::Verified => 'info',
            static::Resolved => 'success',
        };
    }

    public function isResolved(): bool
    {
        return $this === static::Resolved;
    }
}
