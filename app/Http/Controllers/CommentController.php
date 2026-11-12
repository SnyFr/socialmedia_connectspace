<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentController extends Controller
{
    // Memproses Tambah Komentar
    public function store(Request $request,int $postId)
    {
        // Validasi: Teks komentar atau Gambar lampiran wajib ada
        $request->validate([
            'comment' => 'required_without:image|nullable|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
        }

        Comment::create([
            'post_id' => $postId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'image'   => $imagePath,
        ]);

        return redirect()->to(url()->previous() . '#post-' . $postId);
    }

    public function update(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);

        // 1. OTORISASI: Hanya pemilik komentar yang bisa mengedit
        if ($comment->user_id !== Auth::id()) {
            throw new HttpException(403);
        }

        $hasExistingImage = $comment->image && !$request->boolean('remove_image');

        $request->validate([
            'comment' => ($hasExistingImage ? 'nullable' : 'required_without:image') . '|string',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'comment.required_without' => 'Komentar tidak boleh kosong! Isi teks atau unggah gambar.',
        ]);

        $imagePath = $comment->image;

        if ($request->boolean('remove_image') && $comment->image) {
            if (Storage::disk('public')->exists($comment->image)) {
                Storage::disk('public')->delete($comment->image);
            }
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            if ($comment->image && Storage::disk('public')->exists($comment->image)) {
                Storage::disk('public')->delete($comment->image);
            }
            $imagePath = $request->file('image')->store('comments', 'public');
        }

        $comment->update([
            'comment' => $request->input('comment'),
            'image'   => $imagePath,
        ]);

        return back()->with('success', 'Komentar berhasil diperbarui!');
    }

    // Memproses Hapus Komentar (Hanya Pemilik Komentar)
    public function destroy(string $id)
    {
        $comment = Comment::findOrFail($id);

        // OTORISASI: Hanya pemilik komentar yang bisa menghapus
        if ($comment->user_id !== Auth::id()) {
            throw new HttpException(403);
        }

        // Hapus gambar lampiran komentar jika ada
        if ($comment->image && Storage::disk('public')->exists($comment->image)) {
            Storage::disk('public')->delete($comment->image);
        }

        $comment->delete();

        return back();
    }
}