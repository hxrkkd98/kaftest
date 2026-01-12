<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Illuminate\Validation\ValidationException;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        // 1. Validate Input
        // Note: We removed the 'current_password' rule because it relies on MySQL.
        // We will check the password manually below.
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user = $request->user();

        try {
            $auth = Firebase::auth();

            // 2. Verify the CURRENT password
            // We attempt to sign in with the "old" password. 
            // If this fails, the catch block triggers.
            $auth->signInWithEmailAndPassword($user->email, $request->current_password);

            // 3. Update to the NEW password
            $auth->updateUser($user->uid, [
                'password' => $request->password,
            ]);

            return back()->with('status', 'password-updated');

        } catch (\Exception $e) {
            // 4. Handle "Wrong Password" error
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }
    }
}