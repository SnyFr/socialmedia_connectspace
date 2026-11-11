@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">

            <!-- FORM BUAT POSTINGAN BARU -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex gap-3 align-items-start">
                            <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=38bdf8&color=0f172a' }}"
                                class="rounded-circle border border-secondary border-opacity-25 flex-shrink-0" width="40"
                                height="40" style="width: 40px; height: 40px; object-fit: cover;">

                            <div class="w-100">
                                <!-- Input Teks Status -->
                                <textarea name="content" rows="3" class="form-control mb-2"
                                    placeholder="Apa yang sedang terjadi di ConnectSpace?"></textarea>

                                <!-- Container Preview Gambar Postingan -->
                                <div id="imagePreviewContainer"
                                    class="position-relative mb-3 d-none text-center p-2 rounded-3"
                                    style="background-color: #090d16; border: 1px dashed rgba(255, 255, 255, 0.2);">
                                    <img id="postImagePreview" src="" class="img-fluid rounded-3"
                                        style="max-height: 250px; width: auto; object-fit: contain;">
                                    <button type="button"
                                        class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2 shadow"
                                        onclick="removePostImage()" title="Batalkan Gambar">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <label
                                            class="btn btn-sm btn-outline-secondary rounded-pill cursor-pointer border-opacity-25">
                                            <i class="bi bi-image text-info me-1"></i> Lampirkan Gambar
                                            <input type="file" name="image" id="postImageInput" class="d-none"
                                                accept="image/*" onchange="previewPostImage(event)">
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
                <div class="card shadow-sm mb-3" id="post-{{ $post->id }}">
                    <div class="card-body p-3">

                        <!-- Header Post: Foto, Nama, Waktu, & Dropdown Menu Dots 3 -->
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div class="d-flex gap-2 align-items-center">
                                <img src="{{ $post->user->avatar ? asset('storage/' . $post->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=38bdf8&color=0f172a' }}"
                                    class="rounded-circle border border-secondary border-opacity-25" width="40"
                                    height="40" style="object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold text-white fs-6">{{ $post->user->name }}</h6>
                                    <small class="text-muted-custom">{{ $post->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            <!-- Menu Dots 3 (Hanya untuk pemilik post) -->
                            @if (Auth::id() === $post->user_id)
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted-custom p-0 border-0" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                    </button>
                                    <ul
                                        class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary border-opacity-25">
                                        <li>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus postingan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="dropdown-item text-danger d-flex align-items-center gap-2">
                                                    <i class="bi bi-trash"></i> Hapus Feed
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <!-- Isi Teks Status -->
                        @if ($post->content)
                            <p class="card-text mb-2 text-light" style="white-space: pre-line;">{{ $post->content }}</p>
                        @endif

                        <!-- Gambar Status (Jika Ada) -->
                        @if ($post->image)
                            <div class="mb-3 text-left">
                                <img src="{{ asset('storage/' . $post->image) }}"
                                    class="img-fluid rounded-3 border border-secondary border-opacity-25"
                                    style="max-height: 500px; width: auto; max-width: 100%;">
                            </div>
                        @endif

                        <!-- Tombol Action: Like & Komentar (Garis Muted Border-Opacity-25) -->
                        <div
                            class="d-flex align-items-center gap-4 py-2 border-top border-bottom border-secondary border-opacity-25">
                            <form action="{{ route('posts.like', $post->id) }}" method="POST"
                                class="d-flex align-items-center m-0">
                                @csrf
                                @php
                                    $isLiked = $post->likes->contains('user_id', Auth::id());
                                @endphp
                                <button type="submit"
                                    class="btn btn-link p-0 text-decoration-none d-flex align-items-center gap-1 {{ $isLiked ? 'text-danger' : 'text-muted-custom' }} fw-semibold small">
                                    <i class="bi {{ $isLiked ? 'bi-heart-fill' : 'bi-heart' }} fs-6"></i>
                                    <span>{{ $post->likes->count() }} Like</span>
                                </button>
                            </form>

                            <div class="d-flex align-items-center gap-1 text-muted-custom fw-semibold small">
                                <i class="bi bi-chat fs-6"></i>
                                <span>{{ $post->comments->count() }} Komentar</span>
                            </div>
                        </div>

                        <!-- AREA KOMENTAR -->
                        <div class="mt-3">
                            <!-- Form Komentar (Attachment Icon di Dalam Input Group) -->
                            <form action="{{ route('comments.store', $post->id) }}" method="POST"
                                enctype="multipart/form-data" class="mb-3">
                                @csrf
                                <div class="d-flex gap-2 align-items-start">
                                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=38bdf8&color=0f172a' }}"
                                        class="rounded-circle" width="32" height="32" style="object-fit: cover;">
                                    <div class="w-100">
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="comment" class="form-control"
                                                placeholder="Tulis komentar...">

                                            <!-- Icon Attachment tanpa teks -->
                                            <label
                                                class="btn btn-outline-secondary border-secondary border-opacity-25 text-muted-custom d-flex align-items-center px-2 cursor-pointer"
                                                title="Lampirkan Gambar">
                                                <i class="bi bi-paperclip fs-6"></i>
                                                <input type="file" name="image"
                                                    id="commentImageInput-{{ $post->id }}" class="d-none"
                                                    accept="image/*"
                                                    onchange="previewCommentImage(event, {{ $post->id }})">
                                            </label>

                                            <button class="btn btn-accent px-3 fw-bold" type="submit">Kirim</button>
                                        </div>

                                        <!-- Indicator Preview Gambar Komentar (Muted Border) -->
                                        <div id="commentPreviewContainer-{{ $post->id }}"
                                            class="mt-2 d-none position-relative d-inline-block">
                                            <img id="commentPreviewImg-{{ $post->id }}" src=""
                                                class="rounded border border-secondary border-opacity-25"
                                                style="max-height: 80px;">
                                            <button type="button"
                                                class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-1 p-0 d-flex align-items-center justify-content-center"
                                                style="width: 20px; height: 20px;"
                                                onclick="removeCommentImage({{ $post->id }})">
                                                <i class="bi bi-x" style="font-size: 14px;"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <!-- List Komentar -->
                            @foreach ($post->comments as $comment)
                                <div class="d-flex gap-2 mb-2 p-2 rounded-3" style="background-color: #0f172a;">
                                    <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=38bdf8&color=0f172a' }}"
                                        class="rounded-circle" width="30" height="30" style="object-fit: cover;">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="fw-bold text-white small">{{ $comment->user->name }}</span>
                                            <small class="text-muted-custom"
                                                style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                        </div>

                                        @if ($comment->comment)
                                            <p class="mb-1 text-light small">{{ $comment->comment }}</p>
                                        @endif

                                        @if ($comment->image)
                                            <img src="{{ asset('storage/' . $comment->image) }}"
                                                class="img-fluid rounded border border-secondary border-opacity-25 mt-1"
                                                style="max-height: 150px;">
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

    <script>
        // 1. Function untuk menampilkan preview saat gambar dipilih
        function previewPostImage(event) {
            const input = event.target; // Mengambil elemen <input type="file"> yang memicu event
            const container = document.getElementById('imagePreviewContainer'); // Container preview
            const preview = document.getElementById('postImagePreview'); // Elemen <img> untuk tampung preview
            const fileName = document.getElementById('fileName'); // Elemen teks nama berkas

            // Mencek apakah ada file yang dipilih oleh user
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // URL.createObjectURL membuat URL sementara di memori browser dari berkas lokal
                preview.src = URL.createObjectURL(file);

                // Tampilkan container preview dengan menghapus class Bootstrap 'd-none'
                container.classList.remove('d-none');

                // Tampilkan nama file jika elemennya ada
                if (fileName) {
                    fileName.innerText = file.name;
                }
            }
        }

        // 2. Function untuk membatalkan/menghapus pilihan gambar
        function removePostImage() {
            const input = document.getElementById('postImageInput');
            const container = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('postImagePreview');
            const fileName = document.getElementById('fileName');

            // Reset nilai input file agar berkas tidak ter-upload ke server saat form disubmit
            input.value = '';
            preview.src = '';

            // Sembunyikan kembali container preview dengan menambahkan class 'd-none'
            container.classList.add('d-none');

            // Kosongkan teks nama file
            if (fileName) {
                fileName.innerText = '';
            }
        }

        // Preview Gambar Komentar
        function previewCommentImage(event, postId) {
            const input = event.target;
            const container = document.getElementById(`commentPreviewContainer-${postId}`);
            const preview = document.getElementById(`commentPreviewImg-${postId}`);

            if (input.files && input.files[0]) {
                preview.src = URL.createObjectURL(input.files[0]);
                container.classList.remove('d-none');
            }
        }

        function removeCommentImage(postId) {
            const input = document.getElementById(`commentImageInput-${postId}`);
            const container = document.getElementById(`commentPreviewContainer-${postId}`);
            const preview = document.getElementById(`commentPreviewImg-${postId}`);

            input.value = '';
            preview.src = '';
            container.classList.add('d-none');
        }
    </script>

@endsection
