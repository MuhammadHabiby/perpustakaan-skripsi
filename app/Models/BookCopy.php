<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enum\BookCopyStatus;
use App\Enum\BookCondition;

class BookCopy extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'copy_code',
        'status',
        'condition',
    ];

    protected $casts = [
        'status' => BookCopyStatus::class,
        'condition' => BookCondition::class,
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function lostReports(): HasMany
    {
        return $this->hasMany(LostReport::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', BookCopyStatus::Available);
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', BookCopyStatus::Borrowed);
    }
}
