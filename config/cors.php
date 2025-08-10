<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout'],

    'allowed_origins' => [
        'http://localhost:8080',
        'https://tea-management-frontend.vercel.app',
        'http://app.esamudaay.com', 
        'https://app.esamudaay.com'
    ],

    'allowed_methods' => ['*'],
    'allowed_headers' => ['*'],
    'supports_credentials' => true,
];

