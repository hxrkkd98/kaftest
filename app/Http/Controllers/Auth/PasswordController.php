<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\FirebaseRestTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Password Controller
 * Uses Firebase REST API (HTTP/JSON) for password operations
 */
class PasswordController extends Controller
{
    use FirebaseRestTrait;
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
            // 2. Verify the CURRENT password using REST API
            $this->executeAuthOperation(function () use ($user, $request) {
                $auth = $this->getFirebaseAuth();
                return $auth->signInWithEmailAndPassword($user->email, $request->current_password);
            }, 'Verify current password');

            // 3. Update to the NEW password using REST API
            $this->executeAuthOperation(function () use ($user, $request) {
                $auth = $this->getFirebaseAuth();
                $auth->updateUser($user->uid, [
                    'password' => $request->password,
                ]);
            }, 'Update password');

            return back()->with('status', 'password-updated');

        } catch (\Exception $e) {
            // 4. Handle "Wrong Password" error
            throw ValidationException::withMessages([
                'current_password' => ['The provided password does not match your current password.'],
            ]);
        }
    }
}