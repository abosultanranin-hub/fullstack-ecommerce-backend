<?php

$defaultOrigins = [
    'http://localhost:5173',
    'http://localhost:3000',
    'https://fullstack-store-644.netlify.app',
    'https://ranin-store.netlify.app',
];

$configuredOrigins = array_values(array_filter(array_map(
    static fn ($origin) => rtrim(trim($origin), '/'),
    explode(',', env('FRONTEND_URLS', implode(',', $defaultOrigins)))
)));

$frontendUrl = rtrim((string) env('FRONTEND_URL', ''), '/');
if ($frontendUrl !== '' && !in_array($frontendUrl, $configuredOrigins, true)) {
    $configuredOrigins[] = $frontendUrl;
}

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register', 'forgot-password', 'reset-password'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_values(array_unique($configuredOrigins)),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
