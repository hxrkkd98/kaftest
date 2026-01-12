<?php

return [
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
    | Configure the HTTP client to use REST API instead of gRPC.
    |
    */
    'http_client_options' => [
        'proxy' => env('FIREBASE_HTTP_PROXY'),
        'timeout' => env('FIREBASE_HTTP_TIMEOUT', 120),
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

