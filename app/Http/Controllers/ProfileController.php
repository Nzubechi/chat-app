<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Show the user's profile
   public function showProfile()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    // Update the user's profile information
    public function updateProfile(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:6|confirmed', // Optional: Password update
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Optional: Avatar upload
        ]);

        // Get the authenticated user
        $user = $request->user();

        // Update the user's basic info
        $user->name = $request->name;
        $user->email = $request->email;

        // If the password is provided, update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Handle the avatar file upload
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Save the changes
        $user->save();

        // Redirect back to the profile page with a success message
        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}
