<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;

// Redirect root URL ke Feed
Route::get('/', function () {
    return redirect()->route('feed.index');
});

// GUEST MIDDLEWARE: Hanya bisa diakses jika BELUM login
Route::middleware(['guest'])->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// AUTH MIDDLEWARE: Wajib LOGIN untuk mengakses rute di dalamnya
Route::middleware(['auth'])->group(function () {
    // Route Posting dan Feed
    Route::get('/feed', [PostController::class, 'index'])->name('feed.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::put('/posts/{id}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/{id}/save', [PostController::class, 'toggleSave'])->name('posts.save');
    
    // Komentar dan Like
    Route::post('/posts/{id}/like', [LikeController::class, 'toggle'])->name('posts.like');
    Route::post('/posts/{id}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::put('/comments/{id}', [CommentController::class, 'update'])->name('comments.update'); 
    Route::delete('/comments/{id}', [CommentController::class, 'destroy'])->name('comments.destroy'); 
    Route::post('/comments/{id}/like', [CommentController::class, 'toggleLike'])->name('comments.like');

    // Route Profil
    Route::get('/users/{id}', [ProfileController::class, 'show'])->name('users.show');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // Route Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});