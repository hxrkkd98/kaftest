<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Register Firestore User Provider (REST API only)
        Auth::provider('firestore', function ($app, array $config) {
            return new class implements UserProvider {
                
                public function retrieveById($identifier) {
                    try {
                        // Using Firebase REST API (HTTP/JSON) - NO gRPC
                        $firestore = Firebase::firestore()->database();
                        $doc = $firestore->collection('users')->document($identifier)->snapshot();
                        
                        if ($doc->exists()) {
                            Log::debug('User retrieved via REST API', ['uid' => $identifier]);
                            return new User(array_merge(['uid' => $identifier], $doc->data()));
                        }
                    } catch (\Exception $e) {
                        Log::error('Firestore REST API retrieveById error', [
                            'uid' => $identifier,
                            'error' => $e->getMessage(),
                            'transport' => 'REST (HTTP/JSON)'
                        ]);
                    }
                    return null;
                }
                
                public function retrieveByToken($identifier, $token) { return null; }
                public function updateRememberToken(Authenticatable $user, $token) { }
                public function retrieveByCredentials(array $credentials) { return null; }
                public function validateCredentials(Authenticatable $user, array $credentials) { return true; }
                public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false) { return; }
            };
        });
    }
}