<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan Form Registrasi
    public function showRegister()
    {
        return view('auth.register');
    }

    // Memproses Registrasi & Auto-Login
    public function register(Request $request)
    {
        // Validasi register
        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|string|email|max:150|unique:users',
            'password' => 'required|string|min:6|confirmed', // 'confirmed' mencocokkan input password_confirmation
        ], [
            'email.unique'       => 'Email sudah pernah terdaftar!',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Simpan data pengguna baru dengan enkripsi Hash (bcrypt)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // aktifkan sesi login (Auto-Login)
        Auth::login($user);

        // Redirect langsung ke Feed
        return redirect()->route('feed.index')->with('success', 'Selamat datang! Akun Anda berhasil terdaftar.');
    }

    // Menampilkan Form Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Memproses Login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // memverifikasi password terenkripsi di DB secara otomatis
        if (Auth::attempt($credentials)) {
            // Mencegah serangan Session Fixation
            $request->session()->regenerate();

            return redirect()->intended(route('feed.index'))->with('success', 'Login Sukses!');
        }

        // Jika kombinasi email & password salah
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email', 'password');
    }

    // Memproses Logout
    public function logout(Request $request)
    {
        Auth::logout(); // Hancurkan sesi login di Auth

        $request->session()->invalidate(); // Batalkan sesi Laravel
        $request->session()->regenerateToken(); // Buat ulang CSRF token baru

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}