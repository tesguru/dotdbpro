<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://dnwhouse.com',          // Your main domain
        'https://www.dnwhouse.com',      // With www prefix
        'http://localhost:3000',         // Next.js dev server
        'http://localhost:3001',         // Additional dev ports if needed
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,     // Keep false unless using cookies/auth
];
