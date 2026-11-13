@php
    $depth = $depth ?? 0;
    $replies = $allComments->where('parent_id', $comment->id)->sortBy('created_at');
    $replyCount = $replies->count();
    $uniqueKey = $prefix . $comment->id;
@endphp

<div class="{{ $depth === 0 ? 'mb-2 p-2 rounded-3' : 'mt-2 pt-2 ps-2 ps-sm-3 border-start border-secondary border-opacity-25' }}"
     style="{{ $depth === 0 ? 'background-color: #121215;' : '' }}"
     id="comment-box-{{ $uniqueKey }}">

    <div class="d-flex gap-2">
        <!-- Avatar Pemilik Komentar -->
        <a href="{{ route('users.show', $comment->user_id) }}" class="text-decoration-none flex-shrink-0">
            <img src="{{ $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) . '&background=FF2D20&color=ffffff' }}" 
                 class="rounded-circle" 
                 width="{{ $depth === 0 ? '30' : '24' }}" 
                 height="{{ $depth === 0 ? '30' : '24' }}" 
                 style="width: {{ $depth === 0 ? '30px' : '24px' }}; height: {{ $depth === 0 ? '30px' : '24px' }}; object-fit: cover;">
        </a>

        <div class="w-100 min-w-0">
            <!-- Header: Nama, Lokasi | Waktu, & Dropdown Edit/Hapus -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <a href="{{ route('users.show', $comment->user_id) }}" class="fw-bold text-white small text-decoration-none">
                        {{ $comment->user->name }}
                    </a>
                    <small class="text-muted-custom" style="font-size: {{ $depth === 0 ? '0.72rem' : '0.7rem' }};">
                        @if($comment->location)
                            {{ $comment->location }} <span class="mx-1">|</span>
                        @endif
                        {{ $comment->created_at->diffForHumans() }}
                    </small>
                </div>

                <!-- Menu Edit & Hapus (Khusus Pemilik Komentar) -->
                @if(Auth::id() === $comment->user_id)
                    <div class="dropdown">
                        <button class="btn btn-link text-muted-custom p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots small"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark shadow border-secondary border-opacity-25" style="font-size: 0.85rem;">
                            <li>
                                <button type="button" class="dropdown-item text-light d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#editCommentModal-{{ $uniqueKey }}">
                                    <i class="bi bi-pencil-square text-info"></i> {{ $depth === 0 ? 'Edit Komentar' : 'Edit Balasan' }}
                                </button>
                            </li>
                            <li><hr class="dropdown-divider border-secondary border-opacity-25"></li>
                            <li>
                                <button type="button" 
                                        class="dropdown-item text-danger d-flex align-items-center gap-2" 
                                        onclick="openDeleteModal('{{ route('comments.destroy', $comment->id) }}', '{{ $depth === 0 ? 'Yakin ingin menghapus komentar ini?' : 'Yakin ingin menghapus balasan ini?' }}')">
                                    <i class="bi bi-trash"></i> {{ $depth === 0 ? 'Hapus Komentar' : 'Hapus Balasan' }}
                                </button>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Isi Teks Komentar / Balasan -->
            @if($comment->comment)
                <p class="mb-1 text-light small text-break" style="white-space: pre-line;">{{ $comment->comment }}</p>
            @endif

            <!-- Lampiran Gambar Komentar / Balasan -->
            @if($comment->image)
                <div class="mb-1">
                    <img src="{{ asset('storage/' . $comment->image) }}" 
                         class="img-fluid rounded border border-secondary border-opacity-25 mt-1" 
                         style="max-height: {{ $depth === 0 ? '150px' : '130px' }};">
                </div>
            @endif

            <!-- Baris Aksi: Like, Balas, & Lihat Balasan -->
            <div class="d-flex align-items-center flex-wrap gap-3 mt-1">
                <!-- Tombol Like -->
                <form action="{{ route('comments.like', $comment->id) }}" method="POST" class="d-inline-flex align-items-center m-0">
                    @csrf
                    @php
                        $isCommentLiked = $comment->likes->contains('user_id', Auth::id());
                    @endphp
                    <button type="submit" class="btn btn-link p-0 text-decoration-none d-inline-flex align-items-center gap-1 {{ $isCommentLiked ? 'text-danger' : 'text-muted-custom' }}" style="font-size: 0.74rem;">
                        <i class="bi {{ $isCommentLiked ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        <span>{{ $comment->likes->count() }} Like</span>
                    </button>
                </form>

                <!-- Tombol Balas -->
                <button type="button" 
                        class="btn btn-link p-0 text-decoration-none text-muted-custom d-inline-flex align-items-center gap-1 fw-semibold" 
                        style="font-size: 0.74rem;"
                        onclick="toggleReplyForm('{{ $uniqueKey }}')">
                    <i class="bi bi-reply"></i>
                    <span>Balas</span>
                </button>

                <!-- Tombol Lihat / Sembunyikan Balasan (Muncul jika ada balasan dari siapapun) -->
                @if($replyCount > 0)
                    <button type="button" 
                            id="toggleRepliesBtn-{{ $uniqueKey }}"
                            class="btn btn-link p-0 text-decoration-none text-info d-inline-flex align-items-center gap-1 fw-semibold" 
                            style="font-size: 0.74rem;"
                            data-count="{{ $replyCount }}"
                            onclick="toggleReplies('{{ $uniqueKey }}')">
                        <i class="bi bi-chevron-down" id="toggleRepliesIcon-{{ $uniqueKey }}"></i>
                        <span id="toggleRepliesText-{{ $uniqueKey }}">Lihat balasan ({{ $replyCount }})</span>
                    </button>
                @endif
            </div>

            <!-- FORM BALAS KOMENTAR INI (Hidden sampai tombol Balas diklik) -->
            <div id="replyFormContainer-{{ $uniqueKey }}" class="mt-2 d-none">
                <form action="{{ route('comments.store', $post->id) }}" method="POST" enctype="multipart/form-data" class="nested-reply-form" data-parent-key="{{ $uniqueKey }}">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                    <input type="hidden" name="location" class="auto-comment-location" value="">

                    <div class="d-flex gap-2 align-items-start">
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FF2D20&color=ffffff' }}" 
                             class="rounded-circle flex-shrink-0" width="24" height="24" style="width: 24px; height: 24px; object-fit: cover;">
                        <div class="w-100">
                            <div class="input-group input-group-sm">
                                <input type="text" 
                                       name="comment" 
                                       id="replyInput-{{ $uniqueKey }}" 
                                       class="form-control" 
                                       placeholder="Balas {{ $comment->user->name }}...">
                                
                                <label class="btn btn-outline-secondary border-secondary border-opacity-25 text-muted-custom d-flex align-items-center px-2 cursor-pointer" title="Lampirkan Gambar">
                                    <i class="bi bi-paperclip"></i>
                                    <input type="file" name="image" id="commentImageInput-reply-{{ $uniqueKey }}" class="d-none" accept="image/*" onchange="previewCommentImage(event, 'reply-{{ $uniqueKey }}')">
                                </label>

                                <button class="btn btn-accent px-3 fw-bold" type="submit">Balas</button>
                            </div>

                            <!-- Preview Gambar Balasan -->
                            <div id="commentPreviewContainer-reply-{{ $uniqueKey }}" class="mt-2 d-none position-relative d-inline-block">
                                <img id="commentPreviewImg-reply-{{ $uniqueKey }}" src="" class="rounded border border-secondary border-opacity-25" style="max-height: 70px;">
                                <button type="button" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-1 p-0 d-flex align-items-center justify-content-center" style="width: 18px; height: 18px;" onclick="removeCommentImage('reply-{{ $uniqueKey }}')">
                                    <i class="bi bi-x" style="font-size: 12px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- WADAH BALASAN BERSARANG (Default tersembunyi, terbuka saat klik "Lihat balasan") -->
            @if($replyCount > 0)
                <div id="repliesContainer-{{ $uniqueKey }}" class="replies-wrapper d-none mt-1">
                    @foreach($replies as $reply)
                        @include('partials.comment-item', [
                            'comment'     => $reply,
                            'post'        => $post,
                            'allComments' => $allComments,
                            'prefix'      => $prefix,
                            'depth'       => $depth + 1,
                        ])
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>

<!-- MODAL EDIT KOMENTAR / BALASAN -->
@if(Auth::id() === $comment->user_id)
    <div class="modal fade" id="editCommentModal-{{ $uniqueKey }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-light border border-secondary border-opacity-25" style="background-color: #18181b;">
                <div class="modal-header border-secondary border-opacity-25">
                    <h6 class="modal-title fw-bold">{{ $depth === 0 ? 'Edit Komentar' : 'Edit Balasan' }}</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('comments.update', $comment->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">{{ $depth === 0 ? 'Isi Komentar' : 'Isi Balasan' }}</label>
                            <textarea name="comment" rows="2" class="form-control">{{ $comment->comment }}</textarea>
                        </div>

                        @if($comment->image)
                            <div class="mb-3 p-2 rounded border border-secondary border-opacity-25" style="background-color: #121215;">
                                <small class="text-muted-custom d-block mb-2">Gambar saat ini:</small>
                                <img src="{{ asset('storage/' . $comment->image) }}" class="img-fluid rounded mb-2" style="max-height: 120px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeCommentImg-{{ $uniqueKey }}">
                                    <label class="form-check-label small text-danger" for="removeCommentImg-{{ $uniqueKey }}">
                                        Hapus gambar ini
                                    </label>
                                </div>
                            </div>
                        @endif

                        <div>
                            <label class="form-label small fw-semibold">{{ $comment->image ? 'Ganti Gambar (Opsional)' : 'Lampirkan Gambar (Opsional)' }}</label>
                            <input type="file" name="image" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-secondary border-opacity-25">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-accent px-4">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif