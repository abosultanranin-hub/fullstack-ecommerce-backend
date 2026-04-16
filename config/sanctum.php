<?php

use Laravel\Sanctum\Sanctum;

$defaultStatefulDomains = array_filter([
    'localhost',
    'localhost:3000',
    'localhost:5173',
    '127.0.0.1',
    '127.0.0.1:8000',
    '::1',
    'fullstack-store-644.netlify.app',
    'ranin-store.netlify.app',
    env('APP_URL') ? parse_url(env('APP_URL'), PHP_URL_HOST) : null,
    env('FRONTEND_URL') ? parse_url(env('FRONTEND_URL'), PHP_URL_HOST) : null,
]);

// config/sanctum.php
return [
    'stateful' => array_values(array_filter(array_map(
        static fn ($domain) => trim($domain),
        explode(',', env('SANCTUM_STATEFUL_DOMAINS', implode(',', array_unique($defaultStatefulDomains))))
    ))),

    'expiration' => 525600, // دقيقة (سنة)

    'middleware' => [
        'verify_csrf_token' => App\Http\Middleware\VerifyCsrfToken::class,
    ],
];
