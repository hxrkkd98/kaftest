<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FirebaseRestTrait;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * Profile Controller
 * Uses Firebase REST API (HTTP/JSON) for profile operations
 */
class ProfileController extends Controller
{
    use FirebaseRestTrait;
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // 1. Get the current User ID
        $uid = Auth::user()->uid;
        $data = $request->validated();

        try {
            // 2. Update Firebase Authentication using REST API
            $this->executeAuthOperation(function () use ($uid, $data) {
                $auth = $this->getFirebaseAuth();
                $auth->updateUser($uid, [
                    'email' => $data['email'],
                    'displayName' => $data['name'],
                ]);
            }, 'Update user authentication');

            // 3. Update Firestore using REST API
            $this->updateDocument('users', $uid, [
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            // 4. Manually update the local user object so the UI updates immediately
            $request->user()->name = $data['name'];
            $request->user()->email = $data['email'];

            return Redirect::route('profile.edit')->with('status', 'profile-updated');

        } catch (\Exception $e) {
            return Redirect::route('profile.edit')->withErrors(['email' => 'Update failed : ' . $e->getMessage()]);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required'], // We will validate this manually below
        ]);

        $password = $request->password;
        $email = Auth::user()->email;
        $uid = Auth::user()->uid;

        try {
            // 1. Verify Password using REST API
            $this->executeAuthOperation(function () use ($email, $password) {
                $auth = $this->getFirebaseAuth();
                return $auth->signInWithEmailAndPassword($email, $password);
            }, 'Verify password before deletion');

            // 2. Delete from Firestore using REST API
            $this->deleteDocument('users', $uid);

            // 3. Delete from Firebase Authentication using REST API
            $this->executeAuthOperation(function () use ($uid) {
                $auth = $this->getFirebaseAuth();
                $auth->deleteUser($uid);
            }, 'Delete user from Firebase Auth');

            // 4. Logout
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return Redirect::to('/');

        } catch (\Exception $e) {
            return Redirect::route('profile.edit')->withErrors([
                'password' => 'Incorrect Password',
            ], 'userDeletion');
        }
    }
}