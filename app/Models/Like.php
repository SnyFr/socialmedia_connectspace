<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
    ];

    // Data like ini milik 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Data like ini menempel pada 1 Postingan
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
