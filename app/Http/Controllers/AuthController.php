<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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

    public function gantiPaswordForm()
    {
        return view('auth.gantiPassword');
    }

    
public function updatePassword(Request $request)
{
    $request->validate([
        'password_lama' => 'required',
        'password'      => 'required|min:6|confirmed',
    ]);

    $user = Auth::user();

    if (!$user) {
        abort(403);
    }

    if (!Hash::check($request->password_lama, $user->password)) {
        return back()->withErrors([
            'password_lama' => 'Password lama tidak sesuai'
        ]);
    }

    DB::table('users')
        ->where('id', $user->id)
        ->update([
            'password' => bcrypt($request->password),
        ]);

    Auth::logout();

    return redirect('/login')
        ->with('success', 'Password berhasil diubah, silakan login kembali');
}


}
