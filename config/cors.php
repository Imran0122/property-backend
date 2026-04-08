<?php

return [

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'properties*',
        'properties/*',
        'blogs',
        'blogs/*',
        'login',
        'logout',
        'register',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://test-website-sable-ten.vercel.app',
        'https://test-website-azcz.vercel.app',
        'http://localhost:3000',
        'http://127.0.0.1:3000',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];



// return [

//   'paths' => ['api/*', 'sanctum/csrf-cookie'],

// 'allowed_methods' => ['*'],

// 'allowed_origins' => ['http://localhost:3000', 'http://127.0.0.1:3000'],

// 'allowed_origins_patterns' => [],

// 'allowed_headers' => ['*'],

// 'exposed_headers' => [],

// 'max_age' => 0,

// 'supports_credentials' => true,

// ];

