<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CommentController extends Controller
{
    // Memproses Tambah Komentar
    public function store(Request $request, int $postId)
    {
        $request->validate([
            'parent_id' => 'nullable|integer|exists:comments,id',
            'comment'   => 'required_without:image|nullable|string',
            'location'  => 'nullable|string|max:100',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'comment.required_without' => 'Komentar tidak boleh kosong! Isi teks atau unggah gambar.',
        ]);

        // Simpan parent_id sesuai komentar/balasan spesifik yang dibalas (mendukung nested bertingkat)
        $parentId = null;
        if ($request->filled('parent_id')) {
            $parentComment = Comment::where('id', $request->input('parent_id'))
                ->where('post_id', $postId)
                ->first();

            if ($parentComment) {
                $parentId = $parentComment->id;
            }
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('comments', 'public');
        }

        Comment::create([
            'post_id'   => $postId,
            'parent_id' => $parentId,
            'user_id'   => Auth::id(),
            'comment'   => $request->input('comment'),
            'location'  => $request->input('location'),
            'image'     => $imagePath,
        ]);

        return back()->with('success', $parentId ? 'Balasan terkirim!' : 'Komentar terkirim!');
    }

    public function update(Request $request, string $id)
    {
        $comment = Comment::findOrFail($id);

        if ($comment->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki hak akses untuk mengedit komentar ini!');
        }

        $hasExistingImage = $comment->image && !$request->boolean('remove_image');

        $request->validate([
            'comment'  => ($hasExistingImage ? 'nullable' : 'required_without:image') . '|string',
            'location' => 'nullable|string|max:100',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
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
            'comment'  => $request->input('comment'),
            'image'    => $imagePath,
        ]);

        return back()->with('success', 'Komentar berhasil diperbarui!');
    }

    public function toggleLike(string $id)
    {
        $comment = Comment::findOrFail($id);

        $existing = CommentLike::where('user_id', Auth::id())
            ->where('comment_id', $comment->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            CommentLike::create([
                'user_id'    => Auth::id(),
                'comment_id' => $comment->id,
            ]);
        }

        return back();
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