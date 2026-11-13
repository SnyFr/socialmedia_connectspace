@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 82vh;">
    <div class="col-12" style="max-width: 460px;">
        <div class="card shadow-lg border border-secondary border-opacity-25">
            <!-- Header Card -->
            <div class="card-header border-secondary border-opacity-25 text-center py-4">
                <i class="bi bi-strava text-info fs-1 mb-2 d-block"></i>
                <h4 class="mb-1 fw-bold text-white">Daftar Akun ConnectSpace</h4>
                <small class="text-muted-custom">Buat akun baru untuk bergabung di media sosial internal</small>
            </div>

            <!-- Body Card Form -->
            <div class="card-body p-4">
                <form action="{{ route('register.post') }}" method="POST">
                    @csrf

                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-white">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Alamat Email -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-white">Alamat Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required autocomplete="off">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-white">Password (Min. 6 Karakter)</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="••••••••" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-white">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-accent w-100 py-2">Daftar Sekarang</button>
                </form>
            </div>

            <!-- Footer Card -->
            <div class="card-footer border-secondary border-opacity-25 text-center py-3">
                <span class="text-muted-custom">Sudah punya akun?</span> 
                <a href="{{ route('login') }}" class="text-info text-decoration-none fw-bold ms-1">Login di sini</a>
            </div>
        </div>
    </div>
</div>
@endsection