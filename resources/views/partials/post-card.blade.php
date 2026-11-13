@php
    $prefix = $prefix ?? '';
@endphp

<div class="card shadow-sm mb-3" id="{{ $prefix }}post-{{ $post->id }}">
    <div class="card-body p-3">

        <!-- Header Post: Foto & Nama (Klik ke Profil User) + Menu Dots 3 -->
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('users.show', $post->user_id) }}" class="text-decoration-none flex-shrink-0">
                    <img src="{{ $post->user->avatar ? asset('storage/' . $post->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=FF2D20&color=ffffff' }}"
                        class="rounded-circle border border-secondary border-opacity-25" width="40" height="40"
                        style="width: 40px; height: 40px; object-fit: cover;">
                </a>
                <div>
                    <a href="{{ route('users.show', $post->user_id) }}" class="text-decoration-none">
                        <h6 class="mb-0 fw-bold text-white fs-6">{{ $post->user->name }}</h6>
                    </a>
                    <small class="text-muted-custom">{{ $post->created_at->diffForHumans() }}</small>
                </div>
            </div>

            <!-- Menu Dots 3 Postingan (Khusus Pemilik Post) -->
            @if (Auth::id() === $post->user_id)
                <div class="dropdown">
                    <button class="btn btn-link text-muted-custom p-0 border-0" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul
                        class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary border-opacity-25">
                        <li>
                            <button type="button" class="dropdown-item text-light d-flex align-items-center gap-2"
                                data-bs-toggle="modal"
                                data-bs-target="#editPostModal-{{ $prefix }}{{ $post->id }}">
                                <i class="bi bi-pencil-square text-info"></i> Edit Feed
                            </button>
                        </li>
                        <li>
                            <hr class="dropdown-divider border-secondary border-opacity-25">
                        </li>
                        <li>
                            <button type="button" class="dropdown-item text-danger d-flex align-items-center gap-2"
                                onclick="openDeleteModal('{{ route('posts.destroy', $post->id) }}', 'Yakin ingin menghapus postingan ini? Data dan gambar yang dihapus tidak dapat dikembalikan.')">
                                <i class="bi bi-trash"></i> Hapus Feed
                            </button>
                        </li>
                    </ul>
                </div>
            @endif
        </div>

        <!-- Isi Teks Status -->
        @if ($post->content)
            <p class="card-text mb-2 text-light" style="white-space: pre-line;">{{ $post->content }}</p>
        @endif

        <!-- Gambar Status -->
        @if ($post->image)
            <div class="mb-3 text-center">
                <img src="{{ asset('storage/' . $post->image) }}"
                    class="img-fluid rounded-3 border border-secondary border-opacity-25"
                    style="max-height: 500px; width: auto; max-width: 100%;">
            </div>
        @endif

        <!-- Tombol Action: Like, Komentar, & Simpan Postingan -->
        <div
            class="d-flex align-items-center justify-content-between py-2 border-top border-bottom border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-4">
                <!-- Toggle Like Post -->
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

                <!-- Jumlah Komentar -->
                <div class="d-flex align-items-center gap-1 text-muted-custom fw-semibold small">
                    <i class="bi bi-chat fs-6"></i>
                    <span>{{ $post->comments->count() }} Komentar</span>
                </div>
            </div>

            <!-- Toggle Simpan Postingan -->
            <form action="{{ route('posts.save', $post->id) }}" method="POST" class="d-flex align-items-center m-0">
                @csrf
                @php
                    $isSaved = $post->savedPosts->contains('user_id', Auth::id());
                @endphp
                <button type="submit"
                    class="btn btn-link p-0 text-decoration-none d-flex align-items-center gap-1 {{ $isSaved ? 'text-info' : 'text-muted-custom' }} fw-semibold small"
                    title="{{ $isSaved ? 'Hapus dari Tersimpan' : 'Simpan Postingan' }}">
                    <i class="bi {{ $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' }} fs-6"></i>
                    <span>{{ $isSaved ? 'Tersimpan' : 'Simpan' }}</span>
                </button>
            </form>
        </div>

        <!-- AREA KOMENTAR -->
        <div class="mt-3">
            <!-- Form Tulis Komentar (+ Lokasi & Gambar) -->
            <form action="{{ route('comments.store', $post->id) }}" method="POST" enctype="multipart/form-data"
                class="mb-3">
                @csrf
                <!-- Terisi otomatis oleh JS (contoh: "Jakarta, Indonesia") -->
                <input type="hidden" name="location" class="auto-comment-location" value="">

                <div class="d-flex gap-2 align-items-start">
                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FF2D20&color=ffffff' }}"
                        class="rounded-circle flex-shrink-0" width="32" height="32"
                        style="width: 32px; height: 32px; object-fit: cover;">
                    <div class="w-100">
                        <div class="input-group input-group-sm">
                            <input type="text" name="comment" class="form-control" placeholder="Tulis komentar...">

                            <!-- Tombol Attachment Gambar -->
                            <label
                                class="btn btn-outline-secondary border-secondary border-opacity-25 text-muted-custom d-flex align-items-center px-2 cursor-pointer"
                                title="Lampirkan Gambar">
                                <i class="bi bi-paperclip fs-6"></i>
                                <input type="file" name="image"
                                    id="commentImageInput-{{ $prefix }}{{ $post->id }}" class="d-none"
                                    accept="image/*"
                                    onchange="previewCommentImage(event, '{{ $prefix }}{{ $post->id }}')">
                            </label>

                            <button class="btn btn-accent px-3 fw-bold" type="submit">Kirim</button>
                        </div>

                        <!-- Preview Gambar Komentar -->
                        <div id="commentPreviewContainer-{{ $prefix }}{{ $post->id }}"
                            class="mt-2 d-none position-relative d-inline-block">
                            <img id="commentPreviewImg-{{ $prefix }}{{ $post->id }}" src=""
                                class="rounded border border-secondary border-opacity-25" style="max-height: 80px;">
                            <button type="button"
                                class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-1 p-0 d-flex align-items-center justify-content-center"
                                style="width: 20px; height: 20px;"
                                onclick="removeCommentImage('{{ $prefix }}{{ $post->id }}')">
                                <i class="bi bi-x" style="font-size: 14px;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- List Komentar Utama (parent_id === null) -->
            @foreach($post->comments->whereNull('parent_id') as $comment)
                @include('partials.comment-item', [
                    'comment'     => $comment,
                    'post'        => $post,
                    'allComments' => $post->comments,
                    'prefix'      => $prefix,
                    'depth'       => 0,
                ])
            @endforeach
        </div>

    </div>
</div>

<!-- MODAL EDIT POSTINGAN (Dengan Validasi Max 500 Karakter) -->
@if (Auth::id() === $post->user_id)
    <div class="modal fade" id="editPostModal-{{ $prefix }}{{ $post->id }}" tabindex="-1"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-light border border-secondary border-opacity-25"
                style="background-color: #18181b;">
                <div class="modal-header border-secondary border-opacity-25">
                    <h5 class="modal-title fw-bold">Edit Postingan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form action="{{ route('posts.update', $post->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="form-label small fw-semibold mb-0">Konten Status</label>
                                <small id="editCharCount-{{ $prefix }}{{ $post->id }}"
                                    class="text-muted-custom">{{ mb_strlen($post->content ?? '') }} / 500</small>
                            </div>
                            <textarea name="content" rows="4" class="form-control" placeholder="Apa yang sedang terjadi?"
                                oninput="validatePostLength(this, 'editCharCount-{{ $prefix }}{{ $post->id }}', 'editCharWarn-{{ $prefix }}{{ $post->id }}', 'editSubmitBtn-{{ $prefix }}{{ $post->id }}')">{{ $post->content }}</textarea>
                            <small id="editCharWarn-{{ $prefix }}{{ $post->id }}"
                                class="text-danger fw-semibold d-none mt-1 d-block">
                                Melebihi batas maksimal 500 karakter!
                            </small>
                        </div>

                        @if ($post->image)
                            <div class="mb-3 p-2 rounded border border-secondary border-opacity-25"
                                style="background-color: #121215;">
                                <small class="text-muted-custom d-block mb-2">Gambar saat ini:</small>
                                <img src="{{ asset('storage/' . $post->image) }}" class="img-fluid rounded mb-2"
                                    style="max-height: 180px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_image"
                                        value="1" id="removePostImg-{{ $prefix }}{{ $post->id }}">
                                    <label class="form-check-label small text-danger"
                                        for="removePostImg-{{ $prefix }}{{ $post->id }}">
                                        Hapus gambar dari postingan
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label
                                class="form-label small fw-semibold">{{ $post->image ? 'Ganti Gambar Baru (Opsional)' : 'Tambah Gambar (Opsional)' }}</label>
                            <input type="file" name="image" class="form-control form-control-sm"
                                accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-secondary border-opacity-25">
                        <button type="button" class="btn btn-sm btn-outline-secondary"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="submit" id="editSubmitBtn-{{ $prefix }}{{ $post->id }}"
                            class="btn btn-sm btn-accent px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
