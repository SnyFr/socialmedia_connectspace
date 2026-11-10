<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // 1. Menampilkan Feed Beranda
    public function index()
    {
        // Ambil postingan terbaru (latest = created_at DESC)
        // Eager Loading (with) digunakan untuk mencegah masalah N+1 Query
        $posts = Post::with(['user', 'likes', 'comments.user'])
                    ->latest()
                    ->get();

        return view('feed.index', compact('posts'));
    }

    // 2. Memproses Buat Status Baru
    public function store(Request $request)
    {
        // Validasi: Teks ATAU Gambar wajib ada (required_without)
        $request->validate([
            'content' => 'required_without:image|nullable|string',
            'image'   => 'required_without:content|nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'content.required_without' => 'Postingan tidak boleh kosong! Isi teks atau unggah gambar.',
            'image.required_without'   => 'Postingan tidak boleh kosong! Isi teks atau unggah gambar.',
            'image.max'                => 'Ukuran gambar maksimal 2MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            // Simpan gambar ke storage/app/public/posts
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // Simpan status ke database
        Post::create([
            'user_id' => Auth::id(), // ID pengguna yang sedang login
            'content' => $request->input('content'),
            'image'   => $imagePath,
        ]);

        return back()->with('success', 'Status berhasil dipublikasikan!');
    }

    // 3. Memproses Hapus Status (Otorisasi Pemilik)
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);

        // OTORISASI UJK: Cek apakah yang menghapus adalah pemilik asli post
        if ($post->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk menghapus postingan ini!');
        }

        // Hapus berkas gambar dari storage jika post memiliki gambar
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        $post->delete();

        return back()->with('success', 'Postingan berhasil dihapus.');
    }
}