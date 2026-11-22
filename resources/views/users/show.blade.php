@extends('layouts.app')

@section('content')
@php
    $avatarUrl = $user->avatar
        ? asset('storage/' . $user->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=FF2D20&color=ffffff';
@endphp

<div class="row justify-content-center">
    <div class="col-12">

        <!-- KARTU INFORMASI PROFIL -->
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start justify-content-between gap-3">
                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start gap-3 text-center text-sm-start">
                        <button type="button" class="btn p-0 border-0 bg-transparent rounded-circle flex-shrink-0"
                                data-bs-toggle="modal" data-bs-target="#profileAvatarPreview"
                                aria-label="Lihat foto profil {{ $user->name }}">
                            <img src="{{ $avatarUrl }}" alt="{{ $user->name }}"
                                 class="rounded-circle border border-secondary border-opacity-50"
                                 width="80" height="80" style="width: 80px; height: 80px; object-fit: cover;">
                        </button>
                        
                        <div>
                            <h4 class="fw-bold text-white mb-0">{{ $user->name }}</h4>
                            <small class="text-muted-custom d-block mb-2">{{ $user->email }}</small>
                            
                            <p class="text-light small mb-3" style="white-space: pre-line;">
                                {{ $user->bio ?: 'Belum ada bio profil.' }}
                            </p>

                            <div class="d-flex justify-content-center justify-content-sm-start gap-3 small">
                                <span class="text-muted-custom">
                                    <strong class="text-white">{{ $posts->count() }}</strong> Postingan
                                </span>
                                @if(Auth::id() === $user->id)
                                    <span class="text-muted-custom">
                                        <strong class="text-white">{{ $savedPosts->count() }}</strong> Tersimpan
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Edit Profil (Khusus Pemilik Akun Sendiri) -->
                    @if(Auth::id() === $user->id)
                        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold flex-shrink-0">
                            <i class="bi bi-pencil-square me-1"></i> Edit Profil
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="modal fade" id="profileAvatarPreview" tabindex="-1"
             aria-labelledby="profileAvatarPreviewLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-light border border-secondary border-opacity-25"
                     style="background-color: #18181b;">
                    <div class="modal-header border-secondary border-opacity-25">
                        <h5 class="modal-title fw-bold" id="profileAvatarPreviewLabel">{{ $user->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Tutup"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ $avatarUrl }}" alt="Foto profil {{ $user->name }}"
                             class="rounded-circle border border-secondary border-opacity-50"
                             style="width: min(100%, 360px); aspect-ratio: 1; object-fit: cover;">
                    </div>
                </div>
            </div>
        </div>

        <!-- JIKA PROFIL PEMILIK AKUN SENDIRI: TAMPILKAN TAB POSTINGAN & POST TERSIMPAN -->
        @if(Auth::id() === $user->id)
            <ul class="nav nav-pills mb-3 gap-2 p-1 rounded-pill border border-secondary border-opacity-25" style="background-color: #18181b;" id="profileTabs" role="tablist">
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link active w-100 rounded-pill fw-semibold small text-white" 
                            id="own-posts-tab" data-bs-toggle="pill" data-bs-target="#own-posts-pane" type="button" role="tab">
                        <i class="bi bi-grid-3x3-gap me-1"></i> Postingan Saya ({{ $posts->count() }})
                    </button>
                </li>
                <li class="nav-item flex-fill" role="presentation">
                    <button class="nav-link w-100 rounded-pill fw-semibold small text-white" 
                            id="saved-posts-tab" data-bs-toggle="pill" data-bs-target="#saved-posts-pane" type="button" role="tab">
                        <i class="bi bi-bookmark-fill me-1"></i> Post Tersimpan ({{ $savedPosts->count() }})
                    </button>
                </li>
            </ul>

            <style>
                #profileTabs .nav-link.active {
                    background-color: #FF2D20 !important;
                    color: #ffffff !important;
                }
            </style>

            <div class="tab-content" id="profileTabsContent">
                <!-- TAB 1: POSTINGAN MILIK SENDIRI -->
                <div class="tab-pane fade show active" id="own-posts-pane" role="tabpanel">
                    @forelse($posts as $post)
                        @include('partials.post-card', ['post' => $post, 'prefix' => 'own-'])
                    @empty
                        <div class="text-center py-5 text-muted-custom card">
                            <i class="bi bi-chat-square-dots fs-1 d-block mb-2"></i>
                            <p class="mb-0">Anda Belum punya Postingan nih!</p>
                        </div>
                    @endforelse
                </div>

                <!-- TAB 2: POST TERSIMPAN (MILIK SENDIRI MAUPUN ORANG LAIN) -->
                <div class="tab-pane fade" id="saved-posts-pane" role="tabpanel">
                    @forelse($savedPosts as $post)
                        @include('partials.post-card', ['post' => $post, 'prefix' => 'saved-'])
                    @empty
                        <div class="text-center py-5 text-muted-custom card">
                            <i class="bi bi-bookmark fs-1 d-block mb-2"></i>
                            <p class="mb-0">Belum ada postingan tersimpan. Klik tombol <strong>Simpan</strong> pada postingan di feed untuk menyimpannya ke sini.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        <!-- JIKA PROFIL PENGGUNA LAIN: TAMPILKAN POSTINGAN MILIK PENGGUNA TERSEBUT -->
        @else
            <h6 class="fw-bold text-white mb-3 px-1">
                <i class="bi bi-collection text-info me-1"></i> Postingan {{ $user->name }}
            </h6>

            @forelse($posts as $post)
                @include('partials.post-card', ['post' => $post, 'prefix' => 'user-'])
            @empty
                <div class="text-center py-5 text-muted-custom card">
                    <i class="bi bi-chat-left-dots fs-1 d-block mb-2"></i>
                    <p class="mb-0">{{ $user->name }} belum memiliki postingan.</p>
                </div>
            @endforelse
        @endif

    </div>
</div>
@endsection