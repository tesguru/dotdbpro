<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://dnwhouse.com',
        'https://www.dnwhouse.com',
        'http://localhost:3000',
        'http://localhost:3001',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => ['Authorization'],  // Important for JWT tokens

    'max_age' => 86400,  // Cache preflight for 24 hours (improves performance)

    'supports_credentials' => true,  // Required for Authorization headers
];
