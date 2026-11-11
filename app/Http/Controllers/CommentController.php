<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}