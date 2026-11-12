@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h4 class="fw-bold mb-4 text-white">
                <i class="bi bi-house-door-fill me-2 text-info"></i>Beranda
            </h4>

            <!-- FORM BUAT POSTINGAN BARU -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex gap-3 align-items-start">
                            <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FF2D20&color=ffffff' }}"
                                class="rounded-circle border border-secondary border-opacity-25 flex-shrink-0"
                                width="40" height="40" style="width: 40px; height: 40px; object-fit: cover;">

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
                                <img src="{{ $post->user->avatar ? asset('storage/' . $post->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) . '&background=FF2D20&color=ffffff' }}"
                                    class="rounded-circle border border-secondary border-opacity-25 flex-shrink-0"
                                    width="40" height="40" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <h6 class="mb-0 fw-bold text-white fs-6">{{ $post->user->name }}</h6>
                                    <small class="text-muted-custom">{{ $post->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            <!-- Menu Dots 3 Postingan (Hanya Pemilik Post) -->
                            @if (Auth::id() === $post->user_id)
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted-custom p-0 border-0" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                    </button>
                                    <ul
                                        class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary border-opacity-25">
                                        <li>
                                            <button type="button"
                                                class="dropdown-item text-light d-flex align-items-center gap-2"
                                                data-bs-toggle="modal" data-bs-target="#editPostModal-{{ $post->id }}">
                                                <i class="bi bi-pencil-square text-info"></i> Edit Feed
                                            </button>
                                        </li>
                                        <li>
                                            <hr class="dropdown-divider border-secondary border-opacity-25">
                                        </li>
                                        <li>
                                            <form action="{{ route('posts.destroy', $post->id) }}" method="POST"
                                                data-delete-confirm="Yakin ingin menghapus postingan ini?">
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

                        <!-- Tombol Action: Like & Komentar -->
                        <div
                            class="d-flex align-items-center gap-4 py-2 border-top border-bottom border-secondary border-opacity-25">
                            <form action="{{ route('posts.like', $post->id) }}" method="POST"
                                class="d-flex align-items-center m-0">
                                @csrf
                                @php
                                    $isLiked = $post->liked_by_user;
                                @endphp
                                <button type="submit"
                                    class="btn btn-link p-0 text-decoration-none d-flex align-items-center gap-1 {{ $isLiked ? 'text-danger' : 'text-muted-custom' }} fw-semibold small">
                                    <i class="bi {{ $isLiked ? 'bi-heart-fill' : 'bi-heart' }} fs-6"></i>
                                    <span>{{ $post->likes_count }} Like</span>
                                </button>
                            </form>

                            <div class="d-flex align-items-center gap-1 text-muted-custom fw-semibold small">
                                <i class="bi bi-chat fs-6"></i>
                                <span>{{ $post->comments_count }} Komentar</span>
                            </div>
                        </div>

                        <!-- AREA KOMENTAR -->
                        <div class="mt-3">
                            <!-- Form Tulis Komentar -->
                            <form action="{{ route('comments.store', $post->id) }}" method="POST"
                                enctype="multipart/form-data" class="mb-3">
                                @csrf
                                <div class="d-flex gap-2 align-items-start">
                                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FF2D20&color=ffffff' }}"
                                        class="rounded-circle flex-shrink-0" width="32" height="32"
                                        style="width: 32px; height: 32px; object-fit: cover;">
                                    <div class="w-100">
                                        <div class="input-group input-group-sm">
                                            <input type="text" name="comment" class="form-control"
                                                placeholder="Tulis komentar...">

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

                                        <!-- Preview Gambar Komentar -->
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
                                <div class="d-flex gap-2 mb-2 p-2 rounded-3" style="background-color: #121215;">
                                    <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=FF2D20&color=ffffff' }}"
                                        class="rounded-circle flex-shrink-0" width="30" height="30"
                                        style="width: 30px; height: 30px; object-fit: cover;">
                                    <div class="w-100">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="fw-bold text-white small">{{ $comment->user->name }}</span>
                                                <small class="text-muted-custom"
                                                    style="font-size: 0.72rem;">{{ $comment->created_at->diffForHumans() }}</small>
                                            </div>

                                            <!-- Menu Aksi Komentar: Muncul untuk Pemilik Komentar ATAU Pemilik Post -->
                                            @if (Auth::id() === $comment->user_id)
                                                <div class="dropdown">
                                                    <button class="btn btn-link text-muted-custom p-0 border-0"
                                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bi bi-three-dots small"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary border-opacity-25"
                                                        style="font-size: 0.85rem;">
                                                        <li>
                                                            <button type="button"
                                                                class="dropdown-item text-light d-flex align-items-center gap-2"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editCommentModal-{{ $comment->id }}">
                                                                <i class="bi bi-pencil-square text-info"></i> Edit Komentar
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <hr
                                                                class="dropdown-divider border-secondary border-opacity-25">
                                                        </li>
                                                        <li>
                                                            <form action="{{ route('comments.destroy', $comment->id) }}"
                                                                method="POST"
                                                                data-delete-confirm="Yakin ingin menghapus komentar ini?">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item text-danger d-flex align-items-center gap-2">
                                                                    <i class="bi bi-trash"></i> Hapus Komentar
                                                                </button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            @endif

                                            <!-- Hapus: Pemilik Komentar ATAU Pemilik Postingan -->
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

                                <!-- MODAL EDIT KOMENTAR (Khusus Pemilik Komentar) -->
                                @if (Auth::id() === $comment->user_id)
                                    <div class="modal fade" id="editCommentModal-{{ $comment->id }}" tabindex="-1"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content text-light border border-secondary border-opacity-25"
                                                style="background-color: #18181b;">
                                                <div class="modal-header border-secondary border-opacity-25">
                                                    <h6 class="modal-title fw-bold">Edit Komentar</h6>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('comments.update', $comment->id) }}"
                                                    method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label small fw-semibold">Isi
                                                                Komentar</label>
                                                            <textarea name="comment" rows="2" class="form-control">{{ $comment->comment }}</textarea>
                                                        </div>

                                                        @if ($comment->image)
                                                            <div class="mb-3 p-2 rounded border border-secondary border-opacity-25"
                                                                style="background-color: #121215;">
                                                                <small class="text-muted-custom d-block mb-2">Gambar saat
                                                                    ini:</small>
                                                                <img src="{{ asset('storage/' . $comment->image) }}"
                                                                    class="img-fluid rounded mb-2"
                                                                    style="max-height: 120px;">
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        name="remove_image" value="1"
                                                                        id="removeCommentImg-{{ $comment->id }}">
                                                                    <label class="form-check-label small text-danger"
                                                                        for="removeCommentImg-{{ $comment->id }}">
                                                                        Hapus gambar ini
                                                                    </label>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <div>
                                                            <label
                                                                class="form-label small fw-semibold">{{ $comment->image ? 'Ganti Gambar (Opsional)' : 'Lampirkan Gambar (Opsional)' }}</label>
                                                            <input type="file" name="image"
                                                                class="form-control form-control-sm" accept="image/*">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-secondary border-opacity-25">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                                            data-bs-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-sm btn-accent px-4">Simpan
                                                            Perubahan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>

                <!-- MODAL EDIT POSTINGAN (Khusus Pemilik Post) -->
                @if (Auth::id() === $post->user_id)
                    <div class="modal fade" id="editPostModal-{{ $post->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content text-light border border-secondary border-opacity-25"
                                style="background-color: #18181b;">
                                <div class="modal-header border-secondary border-opacity-25">
                                    <h5 class="modal-title fw-bold">Edit Postingan</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('posts.update', $post->id) }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Konten Status</label>
                                            <textarea name="content" rows="4" class="form-control" placeholder="Apa yang sedang terjadi?">{{ $post->content }}</textarea>
                                        </div>

                                        @if ($post->image)
                                            <div class="mb-3 p-2 rounded border border-secondary border-opacity-25"
                                                style="background-color: #121215;">
                                                <small class="text-muted-custom d-block mb-2">Gambar saat ini:</small>
                                                <img src="{{ asset('storage/' . $post->image) }}"
                                                    class="img-fluid rounded mb-2" style="max-height: 180px;">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_image"
                                                        value="1" id="removePostImg-{{ $post->id }}">
                                                    <label class="form-check-label small text-danger"
                                                        for="removePostImg-{{ $post->id }}">
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
                                        <button type="submit" class="btn btn-sm btn-accent px-4">Simpan
                                            Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            @empty
                <div class="text-center py-5 text-muted-custom">
                    <i class="bi bi-chat-left-dots fs-1 d-block mb-2"></i>
                    <p>Belum ada postingan. Jadilah yang pertama membuat status!</p>
                </div>
            @endforelse

        </div>
    </div>

    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmTitle"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-light border border-secondary border-opacity-25"
                style="background-color: #18181b;">
                <div class="modal-header border-secondary border-opacity-25">
                    <h5 class="modal-title fw-bold" id="deleteConfirmTitle">Konfirmasi penghapusan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="deleteConfirmMessage"></div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-sm btn-danger" id="confirmDeleteButton">Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let pendingDeleteForm = null;
        const deleteConfirmModalElement = document.getElementById('deleteConfirmModal');
        let deleteConfirmModal = null;

        function getDeleteConfirmModal() {
            if (!deleteConfirmModal && window.bootstrap?.Modal) {
                deleteConfirmModal = new window.bootstrap.Modal(deleteConfirmModalElement);
            }

            return deleteConfirmModal;
        }

        function submitConfirmedDelete(form) {
            sessionStorage.setItem('scrollPosition', JSON.stringify({
                path: window.location.pathname,
                y: window.scrollY
            }));
            form.submit();
        }

        document.addEventListener('submit', function (event) {
            const form = event.target;
            if (!form.matches('form[data-delete-confirm]')) {
                return;
            }

            event.preventDefault();
            const modal = getDeleteConfirmModal();
            if (!modal) {
                if (window.confirm(form.dataset.deleteConfirm)) {
                    submitConfirmedDelete(form);
                }
                return;
            }

            pendingDeleteForm = form;
            document.getElementById('deleteConfirmMessage').textContent = form.dataset.deleteConfirm;
            modal.show();
        });

        document.getElementById('confirmDeleteButton').addEventListener('click', function () {
            const modal = getDeleteConfirmModal();
            if (pendingDeleteForm && modal) {
                submitConfirmedDelete(pendingDeleteForm);
                pendingDeleteForm = null;
            }
            modal?.hide();
        });

        deleteConfirmModalElement.addEventListener('hidden.bs.modal', function () {
            pendingDeleteForm = null;
        });

        // 1. Function untuk menampilkan preview saat gambar dipilih
        function setImagePreview(preview, file) {
            if (preview.dataset.objectUrl) {
                URL.revokeObjectURL(preview.dataset.objectUrl);
            }

            preview.dataset.objectUrl = URL.createObjectURL(file);
            preview.src = preview.dataset.objectUrl;
        }

        function clearImagePreview(preview) {
            if (preview.dataset.objectUrl) {
                URL.revokeObjectURL(preview.dataset.objectUrl);
                delete preview.dataset.objectUrl;
            }

            preview.removeAttribute('src');
        }

        function previewPostImage(event) {
            const input = event.target; // Mengambil elemen <input type="file"> yang memicu event
            const container = document.getElementById('imagePreviewContainer'); // Container preview
            const preview = document.getElementById('postImagePreview'); // Elemen <img> untuk tampung preview
            const fileName = document.getElementById('fileName'); // Elemen teks nama berkas

            // Mencek apakah ada file yang dipilih oleh user
            if (input.files && input.files[0]) {
                const file = input.files[0];

                // URL.createObjectURL membuat URL sementara di memori browser dari berkas lokal
                setImagePreview(preview, file);

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
            clearImagePreview(preview);

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
                setImagePreview(preview, input.files[0]);
                container.classList.remove('d-none');
            }
        }

        function removeCommentImage(postId) {
            const input = document.getElementById(`commentImageInput-${postId}`);
            const container = document.getElementById(`commentPreviewContainer-${postId}`);
            const preview = document.getElementById(`commentPreviewImg-${postId}`);

            input.value = '';
            clearImagePreview(preview);
            container.classList.add('d-none');
        }
    </script>

@endsection
