<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'comment',
        'image',
    ];

    // Komentar ini milik 1 User pengirim
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Komentar ini menempel pada 1 Postingan
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
