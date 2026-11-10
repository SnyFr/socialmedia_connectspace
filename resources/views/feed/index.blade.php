@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        <!-- FORM BUAT POSTINGAN BARU -->
        <div class="card shadow-sm mb-4">
            <div class="card-body p-3">
                <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="d-flex gap-3">
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=38bdf8&color=0f172a' }}" 
                             class="rounded-circle border border-secondary" 
                             width="45" height="45" style="object-fit: cover;">
                        
                        <div class="w-100">
                            <textarea name="content" rows="3" class="form-control mb-2" placeholder="Apa yang sedang terjadi di ConnectSpace?"></textarea>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="btn btn-sm btn-outline-secondary rounded-pill cursor-pointer">
                                        <i class="bi bi-image text-info me-1"></i> Lampirkan Gambar
                                        <input type="file" name="image" class="d-none" accept="image/*" onchange="document.getElementById('fileName').innerText = this.files[0].name">
                                    </label>
                                    <small id="fileName" class="text-muted-custom"></small>
                                </div>

                                <button type="submit" class="btn btn-accent px-4 py-1">Posting</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- DAFTAR FEED POSTINGAN -->
        @forelse($posts as $post)
            <div class="card shadow-sm mb-3">
                <div class="card-body p-3">
                    
                    <!-- Header Post: Foto, Nama, Waktu, & Tombol Hapus -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex gap-2 align-items-center">
                            <img src="{{ $post->user->avatar ? asset('storage/' . $post->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=38bdf8&color=0f172a' }}" 
                                 class="rounded-circle border border-secondary" 
                                 width="40" height="40" style="object-fit: cover;">
                            <div>
                                <h6 class="mb-0 fw-bold text-white fs-6">{{ $post->user->name }}</h6>
                                <small class="text-muted-custom">{{ $post->created_at->diffForHumans() }}</small>
                            </div>
                        </div>

                        <!-- OTORISASI: Tombol Hapus HANYA muncul untuk pemilik post -->
                        @if(Auth::id() === $post->user_id)
                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus postingan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm text-danger p-0 border-0">
                                    <i class="bi bi-trash fs-5"></i>
                                </button>
                            </form>
                        @endif
                    </div>

                    <!-- Isi Teks Status -->
                    @if($post->content)
                        <p class="card-text mb-2 text-light" style="white-space: pre-line;">{{ $post->content }}</p>
                    @endif

                    <!-- Gambar Status (Jika ada) -->
                    @if($post->image)
                        <div class="mb-3 rounded-3 overflow-hidden border border-secondary">
                            <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid w-100" style="max-height: 450px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Tombol Action: Like & Jumlah Komentar -->
                    <div class="d-flex align-items-center gap-4 py-2 border-top border-bottom border-secondary">
                        <!-- Toggle Like Form -->
                        @php
                            $isLiked = $post->likes->contains('user_id', Auth::id());
                        @endphp
                        <form action="{{ route('posts.like', $post->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm border-0 p-0 {{ $isLiked ? 'text-danger' : 'text-muted-custom' }} fw-semibold">
                                <i class="bi {{ $isLiked ? 'bi-heart-fill' : 'bi-heart' }} me-1"></i>
                                <span>{{ $post->likes->count() }} Like</span>
                            </button>
                        </form>

                        <div class="text-muted-custom fw-semibold small">
                            <i class="bi bi-chat me-1"></i>
                            <span>{{ $post->comments->count() }} Komentar</span>
                        </div>
                    </div>

                    <!-- AREA KOMENTAR -->
                    <div class="mt-3">
                        <!-- Form Tulis Komentar -->
                        <form action="{{ route('comments.store', $post->id) }}" method="POST" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            <div class="d-flex gap-2">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=38bdf8&color=0f172a' }}" 
                                     class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                                <div class="w-100">
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="text" name="comment" class="form-control" placeholder="Tulis komentar...">
                                        <button class="btn btn-accent" type="submit">Kirim</button>
                                    </div>
                                    <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                                </div>
                            </div>
                        </form>

                        <!-- List Komentar -->
                        @foreach($post->comments as $comment)
                            <div class="d-flex gap-2 mb-2 p-2 rounded-3" style="background-color: #0f172a;">
                                <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=38bdf8&color=0f172a' }}" 
                                     class="rounded-circle" width="30" height="30" style="object-fit: cover;">
                                <div class="w-100">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-white small">{{ $comment->user->name }}</span>
                                        <small class="text-muted-custom" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                    </div>
                                    
                                    @if($comment->comment)
                                        <p class="mb-1 text-light small">{{ $comment->comment }}</p>
                                    @endif

                                    @if($comment->image)
                                        <img src="{{ asset('storage/' . $comment->image) }}" class="img-fluid rounded border border-secondary mt-1" style="max-height: 150px;">
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted-custom">
                <i class="bi bi-chat-left-dots fs-1 d-block mb-2"></i>
                <p>Belum ada postingan. Jadilah yang pertama membuat status!</p>
            </div>
        @endforelse

    </div>
</div>
@endsection