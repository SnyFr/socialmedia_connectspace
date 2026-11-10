<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // 1. Menampilkan Form Edit Profil & Password
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    // 2. Memproses Perubahan Nama, Bio, dan Upload Avatar
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi format input
        $request->validate(
        [
            'name'   => 'required|string|max:100',
            'bio'    => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Maksimal 2MB
        ], 
        
        [
            'avatar.image' => 'Berkas yang diunggah harus berupa gambar.',
            'avatar.max'   => 'Ukuran foto profil maksimal 2MB.',
        ]);

        $data = [
            'name' => $request->name,
            'bio'  => $request->bio,
        ];

        // Logika Upload Berkas Gambar Avatar
        if ($request->hasFile('avatar')) {
            // Hapus berkas avatar lama di storage jika ada
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan gambar baru ke folder storage/app/public/avatars
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    // 3. Memproses Perubahan Password dengan Verifikasi Password Lama
    public function updatePassword(Request $request)
    {
        $request->validate([
            'old_password' => 'required',
            'password'     => 'required|string|min:6|confirmed',
        ], [
            'old_password.required' => 'Password lama wajib diisi.',
            'password.min'          => 'Password baru minimal 6 karakter.',
            'password.confirmed'    => 'Konfirmasi password baru tidak cocok.',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ATURAN UJK: Verifikasi keabsahan password lama terlebih dahulu
        if (!Hash::check($request->old_password, $user->password)) {
            return back()->withErrors(['old_password' => 'Password lama yang Anda masukkan tidak sesuai!']);
        }

        // Jika cocok, enkripsi dan simpan password baru
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}