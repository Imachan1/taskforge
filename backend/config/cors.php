<?php

$frontendUrl = trim((string) env('FRONTEND_URL', ''));
$frontendOrigin = '';

if ($frontendUrl !== '' && filter_var($frontendUrl, FILTER_VALIDATE_URL)) {
    $parts = parse_url($frontendUrl);

    if ($parts && isset($parts['scheme'], $parts['host'])) {
        $frontendOrigin = sprintf(
            '%s://%s%s',
            $parts['scheme'],
            $parts['host'],
            isset($parts['port']) ? ':'.$parts['port'] : ''
        );
    }
}

$defaultCorsOrigins = array_values(array_filter([
    $frontendOrigin,
    'http://localhost:5173',
    'http://127.0.0.1:5173',
]));

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | The frontend is deployed separately from the API. Keep allowed origins in
    | environment variables so local, preview, and production deployments can
    | be configured without code changes.
    |
    */

    'paths' => ['api/*', 'auth/google/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => array_values(array_unique(array_filter(array_map(
        'trim',
        explode(',', env('CORS_ALLOWED_ORIGINS', implode(',', $defaultCorsOrigins)))
    )))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
