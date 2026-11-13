<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'image',
    ];
    // RELASI ELOQUENT

    // Status ini milik 1 User spesifik
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Status ini bisa memiliki banyak komentar (1:N)
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // Status ini bisa memiliki banyak like (1:N)
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function savedPosts()
    {
        return $this->hasMany(SavedPost::class);
    }
}
