<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Media Storage Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for media file storage paths,
    | including logos, images, and other media assets.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    |
    | The default storage disk to use for media files.
    |
    */
    'disk' => env('MEDIA_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Logo Storage Paths
    |--------------------------------------------------------------------------
    |
    | These paths define where different types of logos and images are stored.
    | You can customize these paths based on your application's needs.
    |
    */
    'paths' => [
        'logo' => env('LOGO_PATH', 'images/logos'),
        'admin_logo' => env('ADMIN_LOGO_PATH', 'images/logos/admin'),
        'favicon' => env('FAVICON_PATH', 'images/favicons'),
        'meta_image' => env('META_IMAGE_PATH', 'images/meta'),
        'maintenance_image' => env('MAINTENANCE_IMAGE_PATH', 'images/maintenance'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    |
    | Configuration for image processing and validation.
    |
    */
    'image' => [
        'max_size' => env('MAX_IMAGE_SIZE', 2048), // KB
        'allowed_extensions' => ['jpeg', 'jpg', 'png', 'gif', 'webp'],
        'quality' => env('IMAGE_QUALITY', 80),
        'create_thumbnails' => env('CREATE_THUMBNAILS', false),
        'thumbnail_size' => [
            'width' => env('THUMBNAIL_WIDTH', 150),
            'height' => env('THUMBNAIL_HEIGHT', 150),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Favicon Settings
    |--------------------------------------------------------------------------
    |
    | Special settings for favicon files.
    |
    */
    'favicon' => [
        'max_size' => env('FAVICON_MAX_SIZE', 1024), // KB
        'allowed_extensions' => ['ico', 'png', 'jpg', 'gif'],
    ],

    /*
    |--------------------------------------------------------------------------
    | URL Generation
    |--------------------------------------------------------------------------
    |
    | Settings for generating URLs to media files.
    |
    */
    'url' => [
        'base_url' => env('MEDIA_BASE_URL', '/storage'),
        'cdn_url' => env('CDN_URL', null),
        'secure' => env('MEDIA_SECURE_URLS', true),
    ],

];
