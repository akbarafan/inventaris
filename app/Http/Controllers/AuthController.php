<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);

        $credentials = filter_var($request->email, FILTER_VALIDATE_EMAIL)
            ? ['email' => $request->email, 'password' => $request->password]
            : ['name' => $request->email, 'password' => $request->password];

        $credentials['is_active'] = true;

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            ActivityLog::log('login', 'Login ke sistem: ' . $request->email);
            return redirect()->route('dashboard')->with('success', 'Login berhasil!');
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        ActivityLog::log('logout', 'Logout: ' . optional(auth()->user())->name);
        Auth::logout();
        $request->session()->regenerate();
        return redirect()->route('login')->with('success', 'Logout berhasil!');
    }
}
