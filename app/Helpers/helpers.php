<?php

use App\Models\Configuracion;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function setting($key, $default = null)
    {
        // Cache settings for 24 hours to avoid DB queries on every request
        $settings = Cache::remember('app_settings', 60 * 60 * 24, function () {
            return Configuracion::all()->pluck('value', 'key');
        });

        return $settings->get($key, $default);
    }
}
