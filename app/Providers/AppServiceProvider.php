<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
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
        Auth::provider('firestore', function ($app, array $config) {
            return new class implements UserProvider {
                
                public function retrieveById($identifier) {
                    try {
                        // Using REST API instead of gRPC
                        $firestore = Firebase::firestore()->database();
                        $doc = $firestore->collection('users')->document($identifier)->snapshot();
                        
                        if ($doc->exists()) {
                            return new User(array_merge(['uid' => $identifier], $doc->data()));
                        }
                    } catch (\Exception $e) {
                        // Log error if needed
                        \Log::error('Firestore retrieveById error: ' . $e->getMessage());
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