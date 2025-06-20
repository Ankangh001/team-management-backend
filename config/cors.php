<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_origins' => [
        'http://localhost:8080',
        'https://tea-management-frontend.vercel.app',
    ],

    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];

