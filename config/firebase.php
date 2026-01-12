<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Transport Mode
    |--------------------------------------------------------------------------
    |
    | This application is configured to use REST API ONLY (no gRPC).
    | The 'ext-grpc' is disabled in composer.json to force REST transport.
    |
    */
    'transport' => 'rest', // REST API only (HTTP/JSON)
    'grpc_enabled' => false, // gRPC is explicitly disabled

    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Path to the Firebase service account credentials JSON file.
    |
    */
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase_credentials.json')),
        'auto_discovery' => false, // Disable auto-discovery to force explicit config
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Database URL
    |--------------------------------------------------------------------------
    |
    | The URL of your Firebase Realtime Database (if used).
    |
    */
    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Storage Bucket
    |--------------------------------------------------------------------------
    |
    | The default Firebase Storage bucket.
    |
    */
    'storage' => [
        'default_bucket' => env('FIREBASE_STORAGE_BUCKET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firestore Settings
    |--------------------------------------------------------------------------
    |
    | Configure Firestore to use REST API instead of gRPC.
    | This avoids the need for gRPC PHP extension.
    |
    */
    'firestore' => [
        'database' => env('FIRESTORE_DATABASE', '(default)'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Store
    |--------------------------------------------------------------------------
    |
    | The cache store to use for caching Firebase tokens and other data.
    |
    */
    'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Enable debug logging for Firebase operations.
    |
    */
    'logging' => [
        'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL'),
        'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL'),
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP Client Options
    |--------------------------------------------------------------------------
    |
    | Configure the HTTP client for REST API operations.
    | All Firebase operations will use HTTP/JSON instead of gRPC binary protocol.
    |
    */
    'http_client_options' => [
        'proxy' => env('FIREBASE_HTTP_PROXY'),
        'timeout' => env('FIREBASE_HTTP_TIMEOUT', 120),
        'connect_timeout' => env('FIREBASE_HTTP_CONNECT_TIMEOUT', 30),
        'verify' => env('FIREBASE_HTTP_VERIFY_SSL', true),
        'headers' => [
            'User-Agent' => 'KAF-Laravel-Firebase-REST/1.0',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | REST API Configuration
    |--------------------------------------------------------------------------
    |
    | Explicit REST API settings for Firebase services.
    |
    */
    'rest_api' => [
        'enabled' => true,
        'base_uri' => env('FIREBASE_REST_BASE_URI', 'https://firestore.googleapis.com'),
        'auth_base_uri' => env('FIREBASE_AUTH_BASE_URI', 'https://identitytoolkit.googleapis.com'),
        'retry_attempts' => env('FIREBASE_REST_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('FIREBASE_REST_RETRY_DELAY', 1000), // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Verifier Cache
    |--------------------------------------------------------------------------
    |
    | Time to live for the verifier cache in seconds.
    |
    */
    'verifier_cache_ttl' => env('FIREBASE_VERIFIER_CACHE_TTL', 3600),
];

