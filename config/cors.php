<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_unique(array_filter(array_map('trim', explode(',', env('CORS_ALLOWED_ORIGINS', 'https://app.cyberduce.com,https://dev.cyberduce.com,https://yesmlr.yesindiaschools.com,http://localhost:3000,http://localhost:5173,http://localhost:8000,http://127.0.0.1:8000')))))),

    'allowed_origins_patterns' => array_values(array_filter(array_merge(
        [
            '#^https?://.*\.cyberduce\.com(:[0-9]+)?$#',
            '#^https?://.*\.yesindiaschools\.com(:[0-9]+)?$#',
        ],
        array_map('trim', explode(',', env('CORS_ALLOWED_ORIGIN_PATTERNS', '')))
    ))),

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
