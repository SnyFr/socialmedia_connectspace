@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <h4 class="fw-bold mb-4 text-white">
                <i class="bi bi-person-gear me-2 text-info"></i>Pengaturan Profil & Keamanan
            </h4>

            <!-- Form 1: Ubah Data Profil & Avatar -->
            <div class="card shadow-sm mb-4 text-white">
                <div class="card-header border-secondary py-3">
                    <h5 class="card-title mb-0 fw-bold fs-6">Informasi Profil</h5>
                </div>
                <div class="card-body p-4">
                    <!-- wajib enctype="multipart/form-data" untuk upload berkas -->
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row align-items-center mb-4">
                            <!-- Display Avatar -->
                            <div class="col-auto">
                                <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=FF2D20&color=ffffff' }}"
                                    class="rounded-circle border border-secondary" width="80" height="80"
                                    style="object-fit: cover;" alt="Avatar">
                            </div>
                            <div class="col">
                                <label class="form-label fw-semibold">Ganti Foto Profil (Avatar)</label>
                                <input type="file" name="avatar"
                                    class="form-control @error('avatar') is-invalid @enderror" accept="image/*">
                                <small class="text-muted-custom">Format: JPG, PNG, WEBP (Maksimal 2MB)</small>
                                {{-- @if ($user->avatar)
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="remove_avatar"
                                            value="1" id="removeAvatar">
                                        <label class="form-check-label small text-danger" for="removeAvatar">
                                            Hapus foto profil
                                        </label>
                                    </div>
                                @endif --}}
                                @error('avatar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bio -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Bio / Deskripsi Singkat</label>
                            <textarea name="bio" rows="3" class="form-control @error('bio') is-invalid @enderror"
                                placeholder="Tuliskan kata-kata mutiara atau posisi kerja Anda...">{{ old('bio', $user->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-accent px-4">Simpan Perubahan Profil</button>
                    </form>
                </div>
            </div>

            <!-- Form 2: Ubah Password -->
            <div class="card shadow-sm text-white">
                <div class="card-header border-secondary py-3">
                    <h5 class="card-title mb-0 fw-bold fs-6">Ubah Kata Sandi (Password)</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Password Lama -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password Saat Ini (Lama)</label>
                            <input type="password" name="old_password"
                                class="form-control @error('old_password') is-invalid @enderror" required
                                placeholder="Masukkan password lama">
                            @error('old_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password Baru -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password Baru (Min. 6 Karakter)</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" required
                                placeholder="Masukkan password baru">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control" required
                                placeholder="Ulangi password baru">
                        </div>

                        <button type="submit" class="btn btn-accent rounded-pill px-4 fw-bold">Update
                            Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
