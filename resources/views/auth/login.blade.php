@extends('layouts.app')

@section('content')
<div class="row justify-content-center align-items-center" style="min-height: 75vh;">
    <div class="col-md-5 col-lg-4">
        <div class="card shadow-lg border border-secondary border-opacity-25">
            <!-- Header Card -->
            <div class="card-header border-secondary border-opacity-25 text-center py-4">
                <i class="bi bi-strava text-info fs-1 mb-2 d-block"></i>
                <h4 class="mb-0 fw-bold text-white">Login ConnectSpace</h4>
                <small class="text-muted-custom">Masukkan akun untuk masuk ke beranda</small>
            </div>

            <!-- Body Card Form -->
            <div class="card-body p-4">
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf

                    <!-- Email (Blank) -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-white">Email</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@email.com" required autocomplete="off">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password (Blank) -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-white">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="new-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tombol Login -->
                    <button type="submit" class="btn btn-accent w-100 py-2">Masuk</button>
                </form>
            </div>

            <!-- Footer Card -->
            <div class="card-footer border-secondary border-opacity-25 text-center py-3">
                <span class="text-muted-custom">Belum punya akun?</span> 
                <a href="{{ route('register') }}" class="text-info text-decoration-none fw-bold ms-1">Daftar sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection