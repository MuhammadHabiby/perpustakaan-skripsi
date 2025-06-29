<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enum\LostReportStatus;

class LostReport extends Model
{
    use HasFactory;

    protected $table = 'lost_reports';

    protected $fillable = [
        'site_user_id',
        'book_copy_id',
        'borrowing_id',
        'resolution_notes',
    ];

    protected $casts = [
        'report_date' => 'datetime',
        'resolution_date' => 'datetime',
        'status' => LostReportStatus::class,
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(SiteUser::class, 'site_user_id');
    }

    public function bookCopy(): BelongsTo
    {
        return $this->belongsTo(BookCopy::class);
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id_verify');
    }

    public function scopeReported($query)
    {
        return $query->where('status', LostReportStatus::Reported);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', LostReportStatus::Verified);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', LostReportStatus::Resolved);
    }
}
