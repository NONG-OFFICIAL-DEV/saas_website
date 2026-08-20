<?php

return [
    'paths' => ['api/*', 'storage/*'],

    'allowed_methods' => ['*'],

    // Frontend is now Nuxt (default dev port 3000, Vite's 5173/5174 no
    // longer apply since that app was migrated away from) + production.
    'allowed_origins' => [
        'http://localhost:3000',
        'http://127.0.0.1:3000',
        'https://www.nexstacktech.com',
        'https://nexstacktech.com',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
