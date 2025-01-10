<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return view('layouts.login');
    }

    public function store(Request $request)
    {

        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials, true)) {
            throw ValidationException::withMessages([
                'email' => 'Authentication Failed',
            ]);
        }

        $user = Auth::user();
        if (!$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account is not active.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/home');

    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
