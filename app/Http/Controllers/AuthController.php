<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showSignupForm()
    {
        if (Auth::check()) {
            // Redirect to inbox if user is already logged in
            return redirect()->route('inbox');
        }
        return view('auth.signup');
    }

    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('inbox');
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            // Redirect to inbox if user is already logged in
            return redirect()->route('inbox');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('inbox');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }
}
