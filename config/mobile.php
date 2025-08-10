<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mobile Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings specifically for mobile layout and functionality
    |
    */

    // Enable/disable console logging based on environment
    'debug_console' => env('APP_DEBUG', false) && in_array(env('APP_ENV'), ['local', 'development']),
    
    // Mobile detection settings
    'force_mobile_detection' => env('MOBILE_FORCE_DETECTION', false),
    'mobile_breakpoint' => env('MOBILE_BREAKPOINT', 991),
    
    // Cache settings for mobile
    'cache_bust_mobile' => env('MOBILE_CACHE_BUST', true),
    'mobile_cache_version' => env('MOBILE_CACHE_VERSION', config('app.version', '1.0.0')),
    
    // Error handling
    'silent_errors' => env('MOBILE_SILENT_ERRORS', !env('APP_DEBUG', false)),
    'error_reporting_level' => env('MOBILE_ERROR_LEVEL', 'production'),
    
    // JavaScript configuration
    'js_config' => [
        'console_enabled' => env('APP_DEBUG', false) && in_array(env('APP_ENV'), ['local', 'development']),
        'error_boundaries' => true,
        'performance_monitoring' => env('MOBILE_PERFORMANCE_MONITOR', false)
    ]
];
