<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FirebaseRestTrait;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

/**
 * User Registration Controller
 * Uses Firebase REST API (HTTP/JSON) for authentication and Firestore
 */
class RegisteredUserController extends Controller
{
    use FirebaseRestTrait;
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // 1. Create User in Firebase Auth using REST API
            $createdUser = $this->executeAuthOperation(function () use ($request) {
                $auth = $this->getFirebaseAuth();
                return $auth->createUser([
                    'email' => $request->email,
                    'password' => $request->password,
                    'displayName' => $request->name,
                ]);
            }, 'Create user in Firebase Auth');

            // 2. Save User to Firestore using REST API
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            
            $this->executeFirestoreOperation(function () use ($createdUser, $userData) {
                $db = $this->getFirestoreDatabase();
                $db->collection('users')
                    ->document($createdUser->uid)
                    ->set(array_merge($userData, ['created_at' => now()->toIso8601String()]));
            }, 'Save user to Firestore');

            // 3. Login
            Auth::login(new User(array_merge(['uid' => $createdUser->uid], $userData)));

            return redirect(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Registration failed: ' . $e->getMessage()]);
        }
    }
}