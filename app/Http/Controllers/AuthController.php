<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Menampilkan formulir login (WF-01).
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Memproses autentikasi kredensial pengguna (NIP atau Email dan Password).
     * Sesuai alur WF-01 & Use Case Card UC-01 (Step 1 & Extension 1a, 3b).
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'NIP atau Email kedinasan wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $loginInput = trim($request->input('username'));

        // Cari pengguna berdasarkan email atau NIP
        $user = User::where('email', $loginInput)
            ->orWhere('nip', $loginInput)
            ->first();

        // 1. Validasi Kredensial (Extension 1a)
        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('username'))->withErrors([
                'username' => 'Kredensial salah: NIP/Email atau kata sandi tidak cocok.',
            ]);
        }

        // 2. Validasi Status Akun (Extension 3b: Akun nonaktif tidak bisa login)
        if ($user->status !== 'Aktif') {
            return back()->withInput($request->only('username'))->withErrors([
                'username' => 'Akun Anda berstatus Nonaktif. Silakan hubungi Administrator Sistem.',
            ]);
        }

        // 3. Buat Session Login
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Selamat datang, ' . $user->name . ' (' . $user->role . ')!');
    }

    /**
     * Mengakhiri sesi pengguna (Logout).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem SIMADA.');
    }
}
