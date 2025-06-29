<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;


class AdminUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function processedLoans(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'admin_user_id_loan');
    }

    public function processedReturns(): HasMany
    {
        return $this->hasMany(Borrowing::class, 'admin_user_id_return');
    }

    public function processedFinePayments(): HasMany
    {
        return $this->hasMany(Fine::class, 'admin_user_id_paid');
    }

    public function verifiedLostReports(): HasMany
    {
        return $this->hasMany(LostReport::class, 'admin_user_id_verify');
    }
}
