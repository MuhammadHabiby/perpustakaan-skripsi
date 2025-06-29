<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enum\FineStatus;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'borrowing_id',
        'amount',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'payment_date' => 'datetime',
        'status' => FineStatus::class,
    ];

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function paymentProcessor(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id_paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->where('status', FineStatus::Unpaid);
    }

    public function scopePaid($query)
    {
        return $query->where('status', FineStatus::Paid);
    }

    public function scopeWaived($query)
    {
        return $query->where('status', FineStatus::Waived);
    }
}
