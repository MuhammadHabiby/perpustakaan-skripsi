<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'site_users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'site_users',
        ],

        'admin' => [
            'driver' => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [
        'site_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\SiteUser::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\AdminUser::class,
        ],
    ],

    'passwords' => [
        'site_users' => [
            'provider' => 'site_users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
