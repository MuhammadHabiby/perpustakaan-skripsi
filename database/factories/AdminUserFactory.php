<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AdminUser>
 */
class AdminUserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nip' => "123456",
            'name' => "Admin",
            'email' => "admin@admin.com",
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
