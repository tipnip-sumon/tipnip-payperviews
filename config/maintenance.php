<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Configuration
    |--------------------------------------------------------------------------
    |
    | These configuration values determine how the maintenance mode page
    | behaves and what information is displayed to users during maintenance.
    |
    */

    'enabled' => env('MAINTENANCE_MODE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Settings
    |--------------------------------------------------------------------------
    */

    'settings' => [
        
        // Default message shown to users
        'default_message' => 'We are currently performing scheduled maintenance to improve your experience. We will be back shortly.',
        
        // Custom maintenance messages for different scenarios
        'messages' => [
            'default' => 'We are currently performing scheduled maintenance to improve your experience.',
            'security_update' => 'We are implementing important security updates to keep your data safe.',
            'feature_upgrade' => 'We are adding exciting new features to enhance your experience.',
            'server_migration' => 'We are migrating to better servers for improved performance.',
            'database_optimization' => 'We are optimizing our database for faster loading times.',
            'emergency' => 'We are addressing an urgent issue to ensure system stability.',
        ],

        // Contact information
        'contact' => [
            'email' => env('MAINTENANCE_CONTACT_EMAIL', 'support@' . parse_url(env('APP_URL', 'localhost'), PHP_URL_HOST)),
            'phone' => env('MAINTENANCE_CONTACT_PHONE', '+1 (555) 123-4567'),
            'twitter' => env('MAINTENANCE_TWITTER', 'https://twitter.com/company'),
            'status_page' => env('MAINTENANCE_STATUS_PAGE', null),
        ],

        // Visual customization
        'appearance' => [
            'theme_color' => env('MAINTENANCE_THEME_COLOR', '#667eea'),
            'background_gradient' => [
                'start' => env('MAINTENANCE_BG_START', '#667eea'),
                'end' => env('MAINTENANCE_BG_END', '#764ba2'),
            ],
            'show_progress_bar' => env('MAINTENANCE_SHOW_PROGRESS', true),
            'show_countdown' => env('MAINTENANCE_SHOW_COUNTDOWN', true),
            'show_contact_info' => env('MAINTENANCE_SHOW_CONTACT', true),
        ],

        // Behavior settings
        'behavior' => [
            'auto_refresh_enabled' => env('MAINTENANCE_AUTO_REFRESH', true),
            'default_refresh_seconds' => env('MAINTENANCE_REFRESH_SECONDS', 300), // 5 minutes
            'default_retry_seconds' => env('MAINTENANCE_RETRY_SECONDS', 3600), // 1 hour
            'show_admin_link' => env('MAINTENANCE_SHOW_ADMIN_LINK', false),
            'admin_secret' => env('MAINTENANCE_ADMIN_SECRET', null),
        ],

        // Social media and external links
        'social_links' => [
            'twitter' => env('MAINTENANCE_TWITTER_URL', null),
            'facebook' => env('MAINTENANCE_FACEBOOK_URL', null),
            'status_page' => env('MAINTENANCE_STATUS_URL', null),
            'blog' => env('MAINTENANCE_BLOG_URL', null),
        ],

        // Predefined maintenance scenarios
        'scenarios' => [
            'quick_fix' => [
                'message' => 'We are applying a quick fix and will be back in a few minutes.',
                'retry_seconds' => 600, // 10 minutes
                'refresh_seconds' => 60, // 1 minute
            ],
            'scheduled_maintenance' => [
                'message' => 'Scheduled maintenance is in progress. We appreciate your patience.',
                'retry_seconds' => 7200, // 2 hours
                'refresh_seconds' => 300, // 5 minutes
            ],
            'emergency_fix' => [
                'message' => 'We are addressing an urgent issue to ensure system stability.',
                'retry_seconds' => 1800, // 30 minutes
                'refresh_seconds' => 120, // 2 minutes
            ],
            'major_update' => [
                'message' => 'We are deploying major updates with exciting new features.',
                'retry_seconds' => 10800, // 3 hours
                'refresh_seconds' => 600, // 10 minutes
            ],
            'security_patch' => [
                'message' => 'We are implementing critical security updates.',
                'retry_seconds' => 3600, // 1 hour
                'refresh_seconds' => 180, // 3 minutes
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed IPs During Maintenance
    |--------------------------------------------------------------------------
    |
    | IP addresses that should bypass maintenance mode
    |
    */

    'allowed_ips' => [
        '127.0.0.1',
        '::1',
        // Add your office/admin IPs here
    ],

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Templates
    |--------------------------------------------------------------------------
    |
    | Different templates for different types of maintenance
    |
    */

    'templates' => [
        'default' => 'errors.503',
        'minimal' => 'errors.503-minimal',
        'detailed' => 'errors.503-detailed',
        'custom' => 'errors.503-custom',
    ],

];
