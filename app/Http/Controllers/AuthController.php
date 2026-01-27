<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (Auth::check()) {
            $role = auth()->user()->role->role;

            switch ($role) {
                case 'admin':
                    return redirect('/admin');
                case 'operator':
                    return redirect('/petugas');
                case 'user':
                    return redirect('/owner');
                default:
                    Auth::logout();
                    return redirect('/login')->withErrors('silahkan login');
            }
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $credentials = $request->only('username', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $role = auth()->user()->role->role;

            switch ($role) {
                case 'admin':
                    return redirect('/admin');
                case 'petugas':
                    return redirect('/petugas');
                case 'owner':
                    return redirect('/owner');
                default:
                    Auth::logout();
                    return redirect('/login')
                        ->withErrors('Role tidak dikenali');
            }
        }

        return back()->withErrors([
            'username' => 'Username atau password salah'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
