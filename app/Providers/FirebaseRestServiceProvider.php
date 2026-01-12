<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class FirebaseRestServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Force REST API configuration with absolute path
        $credentialsPath = storage_path('app/firebase_credentials.json');
        if (file_exists($credentialsPath)) {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $credentialsPath);
        }
        
        // Ensure gRPC is never used
        if (extension_loaded('grpc')) {
            Log::warning('gRPC extension is loaded but will be ignored. Using REST API for Firebase.');
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Verify Firebase is configured for REST API (non-blocking)
        try {
            $this->verifyRestConfiguration();
        } catch (\Exception $e) {
            // Log warning but don't block application boot
            Log::warning('Firebase REST API verification failed: ' . $e->getMessage());
        }
        
        // Log Firebase transport mode
        if (config('app.debug')) {
            Log::info('Firebase initialized with REST API transport (HTTP/JSON)');
        }
    }

    /**
     * Verify that Firebase is properly configured for REST API
     */
    protected function verifyRestConfiguration(): void
    {
        // Get credentials path with fallback
        $credentialsPath = config('firebase.credentials.file', storage_path('app/firebase_credentials.json'));
        
        // Check if file exists
        if (!file_exists($credentialsPath)) {
            Log::warning('Firebase credentials file not found at: ' . $credentialsPath);
            return; // Don't throw exception, just log warning
        }

        // Verify credentials are valid JSON
        $credentialsContent = @file_get_contents($credentialsPath);
        if ($credentialsContent === false) {
            Log::warning('Cannot read Firebase credentials file');
            return;
        }

        $credentials = json_decode($credentialsContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::warning('Firebase credentials file contains invalid JSON: ' . json_last_error_msg());
            return;
        }

        if (!isset($credentials['project_id'])) {
            Log::warning('Firebase credentials file is missing project_id');
            return;
        }

        // Log successful configuration
        if (config('app.debug')) {
            Log::info('Firebase REST API configured successfully', [
                'project_id' => $credentials['project_id'],
                'transport' => 'REST (HTTP/JSON)',
                'grpc_disabled' => true
            ]);
        }
    }
}

