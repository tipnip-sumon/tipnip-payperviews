<?php

return [
    /*
    |--------------------------------------------------------------------------
    | High Traffic Scaling Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration handles scaling optimizations for high concurrent
    | user loads (1000+ simultaneous users).
    |
    */

    // Traffic thresholds
    'traffic_thresholds' => [
        'low' => 100,
        'medium' => 500,
        'high' => 1000,
        'extreme' => 5000,
    ],

    // Performance monitoring intervals (milliseconds)
    'refresh_intervals' => [
        'low_traffic' => 30000,    // 30 seconds
        'medium_traffic' => 45000, // 45 seconds
        'high_traffic' => 60000,   // 1 minute
        'extreme_traffic' => 120000, // 2 minutes
    ],

    // Database optimizations
    'database' => [
        'enable_query_cache' => env('DB_QUERY_CACHE', true),
        'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 10),
        'max_connections' => env('DB_MAX_CONNECTIONS', 200),
        'connection_lifetime' => env('DB_CONNECTION_LIFETIME', 3600),
        'enable_persistent_connections' => env('DB_PERSISTENT', true),
    ],

    // Cache optimizations
    'cache' => [
        'performance_metrics_ttl' => 30, // seconds
        'user_data_ttl' => 300,         // 5 minutes
        'static_data_ttl' => 3600,      // 1 hour
        'enable_distributed_cache' => env('CACHE_DISTRIBUTED', false),
    ],

    // Rate limiting
    'rate_limiting' => [
        'dashboard_requests_per_minute' => 120,
        'api_requests_per_minute' => 60,
        'performance_metrics_requests_per_minute' => 30,
    ],

    // Session optimizations
    'session' => [
        'use_database_sessions' => env('SESSION_USE_DATABASE', true),
        'session_cleanup_probability' => 2, // Lower for high traffic
        'gc_maxlifetime' => 7200, // 2 hours
    ],

    // Memory optimizations
    'memory' => [
        'max_memory_per_request' => '128M',
        'enable_opcache' => true,
        'enable_memory_monitoring' => true,
    ],

    // Load balancing
    'load_balancing' => [
        'enable_sticky_sessions' => true,
        'health_check_interval' => 30,
        'max_request_per_server' => 1000,
    ],

    // Monitoring and alerts
    'monitoring' => [
        'enable_performance_alerts' => true,
        'response_time_threshold' => 1000, // ms
        'memory_usage_threshold' => 80,    // percentage
        'concurrent_user_threshold' => 8000,
        'alert_email' => env('ALERT_EMAIL', 'admin@example.com'),
    ],

    // Emergency mode settings
    'emergency_mode' => [
        'auto_enable_threshold' => 8000, // concurrent users
        'disable_real_time_features' => true,
        'reduce_refresh_rates' => true,
        'enable_static_caching' => true,
        'limit_database_queries' => true,
    ],
];
