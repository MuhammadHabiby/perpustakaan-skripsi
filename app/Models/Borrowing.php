<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enum\BorrowingStatus;

class Borrowing extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_user_id',
        'book_copy_id',
        'booking_id',
        'admin_user_id_loan',
        'borrow_date',
        'due_date',
        'return_date',
        'admin_user_id_return',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'status' => BorrowingStatus::class,
    ];

    public function siteUser(): BelongsTo
    {
        return $this->belongsTo(SiteUser::class);
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function loanProcessor(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id_loan');
    }

    public function returnProcessor(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id_return');
    }

    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    public function lostReport(): HasOne
    {
        return $this->hasOne(LostReport::class, 'borrowing_id');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [BorrowingStatus::Borrowed, BorrowingStatus::Overdue]);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', BorrowingStatus::Overdue);
    }

    public function scopeReturned($query)
    {
        return $query->where('status', BorrowingStatus::Returned);
    }
}
