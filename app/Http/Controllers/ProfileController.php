<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Kreait\Laravel\Firebase\Facades\Firebase;

class ProfileController extends Controller
{
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
            // 2. Update Firebase Authentication (The "Login" Info)
            // This ensures if they change their email here, they must login with the new email next time.
            $auth = Firebase::auth();
            $auth->updateUser($uid, [
                'email' => $data['email'],
                'displayName' => $data['name'],
            ]);

            // 3. Update Firestore (The "Profile" Info)
            $firestore = Firebase::firestore()->database();
            $firestore->collection('users')->document($uid)->set([
                'name' => $data['name'],
                'email' => $data['email'],
                'updated_at' => now()->toIso8601String(),
            ], ['merge' => true]); // 'merge' means "update only these fields, don't delete others"

            // 4. Manually update the local user object so the UI updates immediately
            $request->user()->name = $data['name'];
            $request->user()->email = $data['email'];

            return Redirect::route('profile.edit')->with('status', 'profile-updated');

        } catch (\Exception $e) {
            return Redirect::route('profile.edit')->withErrors(['email' => 'Update failed: ' . $e->getMessage()]);
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
            // 1. Verify Password with Firebase before deleting
            // (We try to sign in. If it fails, the password is wrong)
            Firebase::auth()->signInWithEmailAndPassword($email, $password);

            // 2. Delete from Firestore Database
            Firebase::firestore()->database()->collection('users')->document($uid)->delete();

            // 3. Delete from Firebase Authentication
            Firebase::auth()->deleteUser($uid);

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