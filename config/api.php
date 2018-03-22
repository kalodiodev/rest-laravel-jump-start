<?php

return [

    /**
     * Tokens
     */
    'token' => [
        'access' => [
            'expiration' => env('ACCESS_TOKEN_EXPIRATION_DAYS', 10),
        ],
        'refresh' => [
            'expiration' => env('REFRESH_TOKEN_EXPIRATION_DAYS', 20),
        ]
    ],

    /**
     * CORS Headers
     */
    'cors' => [
        'status' => env('CORS_HEADERS_ENABLED', false),
        'allow' => [
            'origin' => env('CORS_ALLOW_ORIGIN', '*'),
            'methods' => env('CORS_ALLOW_METHODS', 'GET, POST, PUT, PATCH, DELETE, OPTIONS'),
            'headers' => env('CORS_ALLOW_HEADERS',  'Content-Type, Authorization')
        ],
    ],
];