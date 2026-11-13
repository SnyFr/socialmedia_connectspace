<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\User;
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

        $oldAvatar = $user->avatar;

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $data['avatar'] = null;
        }

        $user->update($data);

        if ($oldAvatar && $oldAvatar !== $user->avatar && Storage::disk('public')->exists($oldAvatar)) {
            Storage::disk('public')->delete($oldAvatar);
        }

        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    public function show(string $id)
    {
        $user = User::findOrFail($id);

        // Semua postingan milik user tersebut beserta komentar (sendiri & orang lain)
        $posts = Post::with(['user', 'likes', 'savedPosts', 'comments.user', 'comments.likes', 'comments.replies.user',
    'comments.replies.likes',])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        // Daftar post tersimpan khusus jika sedang membuka profil milik sendiri
        $savedPosts = collect();
        if (Auth::id() === $user->id) {
            $savedPosts = $user->savedPosts()
                ->with(['user', 'likes', 'savedPosts', 'comments.user', 'comments.likes'])
                ->latest('saved_posts.created_at')
                ->get();
        }

        return view('users.show', compact('user', 'posts', 'savedPosts'));
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