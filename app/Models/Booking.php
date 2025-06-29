<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enum\BookingStatus;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_user_id',
        'book_id',
        'book_copy_id',
        'booking_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'booking_date' => 'datetime',
        'expiry_date' => 'datetime',
        'status' => BookingStatus::class,
    ];

    public function siteUser(): BelongsTo
    {
        return $this->belongsTo(SiteUser::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class, 'book_copy_id');
    }

    public function borrowing(): HasOne
    {
        return $this->hasOne(Borrowing::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', BookingStatus::Active);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', BookingStatus::Expired);
    }
}
