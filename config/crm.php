<?php

return [
    /*
    |--------------------------------------------------------------------------
    | CRM API Configuration
    |--------------------------------------------------------------------------
    */
    'api_token' => env('CRM_API_TOKEN'),
    
    'api' => [
        'timeout' => env('CRM_API_TIMEOUT', 30),
        'retry_attempts' => env('CRM_API_RETRY_ATTEMPTS', 3),
    ],
    
    'endpoints' => [
        'register' => '/api/register',
        'update' => '/api/update',
        'delete' => '/api/delete',
    ],
];