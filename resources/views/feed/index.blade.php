@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12">

            <!-- FORM BUAT POSTINGAN BARU (Validasi Live Max 500 Karakter) -->
            <div class="card shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="d-flex gap-3 align-items-start">
                            <a href="{{ route('users.show', Auth::id()) }}" class="text-decoration-none flex-shrink-0">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FF2D20&color=ffffff' }}"
                                    class="rounded-circle border border-secondary border-opacity-25" width="40"
                                    height="40" style="width: 40px; height: 40px; object-fit: cover;">
                            </a>

                            <div class="w-100">
                                <!-- Input Teks Status -->
                                <textarea name="content" id="createPostContent" rows="3"
                                    class="form-control mb-1 @error('content') is-invalid @enderror"
                                    placeholder="Apa yang sedang terjadi di ConnectSpace?"
                                    oninput="validatePostLength(this, 'createCharCount', 'createCharWarning', 'createPostSubmitBtn')">{{ old('content') }}</textarea>

                                @error('content')
                                    <div class="invalid-feedback d-block mb-1">{{ $message }}</div>
                                @enderror

                                <!-- Indikator Counter & Peringatan 500 Karakter -->
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small id="createCharWarning" class="text-danger fw-semibold d-none">
                                        <i class="bi bi-exclamation-circle me-1"></i>Melebihi batas maksimal 500 karakter!
                                    </small>
                                    <small id="createCharCount" class="text-muted-custom ms-auto">0 / 500</small>
                                </div>

                                <!-- Container Preview Gambar Postingan -->
                                <div id="imagePreviewContainer"
                                    class="position-relative mb-3 d-none text-center p-2 rounded-3"
                                    style="background-color: #121215; border: 1px dashed rgba(255, 255, 255, 0.2);">
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

                                    <button type="submit" id="createPostSubmitBtn"
                                        class="btn btn-accent px-4 py-1">Posting</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- DAFTAR FEED POSTINGAN -->
            @forelse($posts as $post)
                @include('partials.post-card', ['post' => $post, 'prefix' => 'feed-'])
            @empty
                <div class="text-center py-5 text-muted-custom">
                    <i class="bi bi-chat-left-dots fs-1 d-block mb-2"></i>
                    <p>Belum ada postingan. Jadilah yang pertama membuat status!</p>
                </div>
            @endforelse

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

        document.addEventListener('submit', function(event) {
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

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            const modal = getDeleteConfirmModal();
            if (pendingDeleteForm && modal) {
                submitConfirmedDelete(pendingDeleteForm);
                pendingDeleteForm = null;
            }
            modal?.hide();
        });

        deleteConfirmModalElement.addEventListener('hidden.bs.modal', function() {
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
            const container = document.getElementById(`commentPreviewContainer-${postId}`);\
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
