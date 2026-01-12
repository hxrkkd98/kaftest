<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FirebaseRestTrait;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Validation\ValidationException;

/**
 * Authentication Controller
 * Uses Firebase REST API (HTTP/JSON) for authentication
 */
class AuthenticatedSessionController extends Controller
{
    use FirebaseRestTrait;
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // 1. Sign In using Firebase Auth REST API
            $signInResult = $this->executeAuthOperation(function () use ($request) {
                $auth = $this->getFirebaseAuth();
                return $auth->signInWithEmailAndPassword($request->email, $request->password);
            }, 'Sign in with email and password');
            
            $uid = $signInResult->firebaseUserId();

            // 2. Get User Data from Firestore using REST API
            $userData = $this->getDocument('users', $uid);

            if (!$userData) {
                throw ValidationException::withMessages(['email' => 'User exists in Auth but not in Database.']);
            }

            // 3. Login manually - ensure uid is set correctly
            $userData['uid'] = $uid; // Ensure uid is set (getDocument returns 'id')
            Auth::login(new User($userData));
            
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials (REST API).',
            ]);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
