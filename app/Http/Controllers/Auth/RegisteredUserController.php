<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Kreait\Laravel\Firebase\Facades\Firebase;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
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
            // 1. Create User in Firebase Auth
            $auth = Firebase::auth();
            $createdUser = $auth->createUser([
                'email' => $request->email,
                'password' => $request->password,
                'displayName' => $request->name,
            ]);

            // 2. Save User to Firestore (Clean Syntax)
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'created_at' => now()->toIso8601String(),
            ];
            
            Firebase::firestore()->database()->collection('users')
                ->document($createdUser->uid)
                ->set($userData);

            // 3. Login
            Auth::login(new User(array_merge(['uid' => $createdUser->uid], $userData)));

            return redirect(route('dashboard', absolute: false));

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Error: ' . $e->getMessage()]);
        }
    }
}