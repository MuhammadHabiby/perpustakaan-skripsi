<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        $settings = Cache::remember('app_settings', 3600, function () {
            return Setting::pluck('value', 'key')->all();
        });

        return $settings[$key] ?? $default;
    }
}
