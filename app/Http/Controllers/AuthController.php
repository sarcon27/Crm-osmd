<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function login()
    {
        return Inertia::render('Auth/Login');
    }

    public function auth(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (auth()->attempt([
            'password' => $request->password,
            'email' => $request->email,
        ])) {

            $request->session()->regenerate();

            return redirect()->route('dashboard.index');

        } else {
            return Inertia::render('Auth/Login', [
                'failed' => 'These credentials do not match any of our records!',
            ]);
        }
    }

    public function logout(Request $request)
    {
        auth()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
