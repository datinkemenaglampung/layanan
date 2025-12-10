<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Helpers\LogActivity;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // Login Method
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Cek apakah user ada dan akun terkunci
        if ($user && $user->locked_until && $user->locked_until > now()) {
            return response()->json(['status' => 'error', 'message' => 'Akun Anda terkunci. Coba lagi nanti.']);
        }

        // Validasi password
        if ($user && Hash::check($request->password, $user->password)) {
            // Reset gagal login jika login berhasil
            $user->update(['failed_login_attempts' => 0, 'locked_until' => null]);

            Auth::login($user);
            LogActivity::add('login', 'User', 1, 'Login Akun');
            return response()->json(['status' => 'success', 'message' => 'Login berhasil!', 'redirect' => route('dashboard')]);
        } else {
            // Menambah percobaan login gagal
            if ($user) {
                $user->increment('failed_login_attempts');

                // Jika gagal login 3 kali, kunci akun
                if ($user->failed_login_attempts >= 3) {
                    LogActivity::add('login', 'User', 0, 'Gagal Login , Akun ' . $user->email . ' diKunci');
                    $user->update(['locked_until' => now()->addMinutes(15)]);
                    return response()->json(['status' => 'error', 'message' => 'Akun Anda terkunci setelah 3 kali percobaan gagal.']);
                }
            }

            return response()->json(['status' => 'error', 'message' => 'Email dan Password Salah!']);
        }
    }

    // Logout Method
    public function logout()
    {
        LogActivity::add('logout', 'User', 0, 'Logout Akun');
        Auth::logout();
        return redirect()->route('login');
    }
}
