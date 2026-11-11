<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    // Toggle Like (Suka / Batal Suka)
    public function toggle(int $postId)
    {
        $userId = Auth::id();

        // Cari apakah user ini sudah pernah nge-like post ini
        $like = Like::where('user_id', $userId)
                    ->where('post_id', $postId)
                    ->first();

        if ($like) {
            // Jika sudah ada, hapus data (Unlike)
            $like->delete();
        } else {
            // Jika belum ada, buat data baru (Like)
            Like::create([
                'user_id' => $userId,
                'post_id' => $postId,
            ]);
        }

        return redirect()->to(url()->previous() . '#post-' . $postId); // Refresh halaman feed
    }
}