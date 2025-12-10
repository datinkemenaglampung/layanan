<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Helpers\LogActivity;

class SSOController extends Controller
{
    public function redirectToProvider()
    {
        // URL redirect ke halaman login SSO Kemenag
        $url = config('services.kemenag_sso.url') . '/auth/signin?appid=' . config('services.kemenag_sso.client_id');
        return redirect($url);
    }

    public function handleProviderCallback(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect('/')->with('error', 'Token tidak ditemukan');
        }

        // Panggil API verify ke SSO Kemenag
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get(config('services.kemenag_sso.url') . '/auth/verify');

        if ($response->failed()) {
            abort(403, 'Gagal menghubungi server SSO Kemenag');
        }

        $data = $response->json();

        // Validasi format response
        if (!isset($data['pegawai'])) {
            abort(403, 'Data user tidak valid dari SSO');
        }

        $userData = $data['pegawai'];

        // 🔍 Cari user berdasarkan username/NIP
        $user = User::where('username', $userData['NIP'])->first();

        if (!$user) {

            $email = $userData['NIP'] . '@kemenag.go.id';

            // Cek user di database berdasarkan NIP atau email
            $user = User::firstOrCreate(
                ['username' => $userData['NIP']],
                [
                    'name' => $userData['NAMA'],
                    'email' => $email,
                    'role_id' => '2',
                    'password' => bcrypt($email),
                ]
            );
        }

        // Login user
        Auth::login($user);

        // Simpan token jika ingin digunakan lagi
        session(['sso_token' => $token]);

        // Log Activity custom
        LogActivity::add('login', 'User', $user->id, 'Login via SSO');

        // Redirect ke dashboard
        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Opsional: redirect ke logout SSO
        return redirect(config('services.kemenag_sso.url') . '/auth/signout');
    }
}
