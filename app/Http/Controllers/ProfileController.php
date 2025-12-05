<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

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
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile with bio, avatar, and banner.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
        ]);

        $updateData = [];

        if ($request->filled('bio')) {
            $updateData['bio'] = $request->input('bio');
        }

        if ($request->hasFile('avatar')) {
            // Delete old avatar if it exists
            if ($user->avatar) {
                if (file_exists(public_path($user->avatar))) {
                    unlink(public_path($user->avatar));
                }
            }
            $avatarFile = $request->file('avatar');
            $avatarName = 'avatars/' . time() . '.' . $avatarFile->getClientOriginalExtension();
            $avatarFile->move(public_path('avatars'), $avatarName);
            $updateData['avatar'] = $avatarName;
        }

        if ($request->hasFile('banner')) {
            // Delete old banner if it exists
            if ($user->banner) {
                if (file_exists(public_path($user->banner))) {
                    unlink(public_path($user->banner));
                }
            }
            $bannerFile = $request->file('banner');
            $bannerName = 'banners/' . time() . '.' . $bannerFile->getClientOriginalExtension();
            $bannerFile->move(public_path('banners'), $bannerName);
            $updateData['banner'] = $bannerName;
        }
        
        if (!empty($updateData)) {
            $user->update($updateData);
        }

        return Redirect::route('profile.show')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
