<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    /*
    |--------------------------------------------------------------------------
    | En producción, reemplazar con el dominio real de la aplicación.
    | La app mobile (React Native) no usa CORS, así que esto solo aplica
    | a browsers que intenten acceder a la API desde otros dominios.
    |--------------------------------------------------------------------------
    */
    'allowed_origins' => array_filter([
        env('APP_URL', 'http://localhost'),
        env('CORS_ALLOWED_ORIGIN'),
    ]),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['Content-Type', 'Authorization', 'Accept', 'X-Requested-With'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => false,

];
