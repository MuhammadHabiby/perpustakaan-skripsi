<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'site_users';

    protected $fillable = [
        'nis',
        'name',
        'email',
        'password',
        'class',
        'major',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'fcm_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }

    public function lostReports(): HasMany
    {
        return $this->hasMany(LostReport::class);
    }
}
