<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PostController extends Controller
{
    // 1. Menampilkan Feed Beranda
    public function index()
    {
        // Ambil postingan terbaru (latest = created_at DESC)
        // Eager Loading (with) digunakan untuk mencegah masalah N+1 Query
        $posts = Post::with(['user', 'comments.user'])
            ->withCount(['likes', 'comments'])
            ->withExists([
                'likes as liked_by_user' => fn ($query) => $query->where('user_id', Auth::id()),
            ])
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

    // Memproses Edit Status (Hanya Pemilik Post)
    public function update(Request $request, string $id)
    {
        $post = Post::findOrFail($id);

        // 1. OTORISASI: Pastikan hanya pemilik post yang bisa mengedit
        if ($post->user_id !== Auth::id()) {
            throw new HttpException(403);
        }

        // 2. Validasi: Cek apakah gambar lama dipertahankan atau dihapus
        $hasExistingImage = $post->image && !$request->boolean('remove_image');

        $request->validate([
            'content' => ($hasExistingImage ? 'nullable' : 'required_without:image') . '|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], [
            'content.required_without' => 'Postingan tidak boleh kosong! Isi teks atau unggah gambar.',
            'image.max'                => 'Ukuran gambar maksimal 2MB.',
        ]);

        $imagePath = $post->image;

        // Jika user mencentang hapus gambar lama
        if ($request->boolean('remove_image') && $post->image) {
            if (Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = null;
        }

        // Jika user mengunggah gambar baru (timpa gambar lama)
        if ($request->hasFile('image')) {
            if ($post->image && Storage::disk('public')->exists($post->image)) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        // 3. Update data ke database
        $post->update([
            'content' => $request->input('content'),
            'image'   => $imagePath,
        ]);

        return back()->with('success', 'Postingan berhasil diupdate!');
    }

    // 3. Memproses Hapus Status (Otorisasi Pemilik)
    public function destroy(string $id)
    {
        $post = Post::findOrFail($id);

        // OTORISASI UJK: Cek apakah yang menghapus adalah pemilik asli post
        if ($post->user_id !== Auth::id()) {
            throw new HttpException(403);
        }

        $commentImages = $post->comments()
            ->whereNotNull('image')
            ->pluck('image');

        // Hapus gambar post dan komentar sebelum record komentar di-cascade oleh database.
        if ($post->image && Storage::disk('public')->exists($post->image)) {
            Storage::disk('public')->delete($post->image);
        }

        foreach ($commentImages as $image) {
            if (Storage::disk('public')->exists($image)) {
                Storage::disk('public')->delete($image);
            }
        }

        $post->delete();

        return back()->with('success', 'Postingan berhasil dihapus.');
    }
}