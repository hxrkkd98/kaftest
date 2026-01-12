<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
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
            // 1. Sign In using the Web API Key (Client Simulation)
            $auth = Firebase::auth();
            $signInResult = $auth->signInWithEmailAndPassword($request->email, $request->password);
            $uid = $signInResult->firebaseUserId();

            // 2. Get User Data from Firestore
            $snapshot = Firebase::firestore()->database()->collection('users')->document($uid)->snapshot();

            if (!$snapshot->exists()) {
                throw ValidationException::withMessages(['email' => 'User exists in Auth but not in Database.']);
            }

            // 3. Login manually
            Auth::login(new User(array_merge(['uid' => $uid], $snapshot->data())));
            
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            throw ValidationException::withMessages([
                'email' => 'Invalid credentials.',
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
