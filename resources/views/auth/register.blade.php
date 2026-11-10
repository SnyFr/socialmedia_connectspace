@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold text-center py-3">
                    <h4 class="mb-0">Daftar Akun ConnectSpace</h4>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('register.post') }}" method="POST">
                        @csrf

                        <!-- Nama Lengkap -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" placeholder="nama@email.com" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password (Min. 6 Karakter)</label>
                            <input type="password" name="password"
                                class="form-control @error('password') is-invalid @enderror" placeholder="••••••••"
                                required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="••••••••"
                                required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Daftar Sekarang</button>
                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    Sudah punya akun? <a href="{{ route('login') }}"
                        class="text-primary text-decoration-none fw-semibold">Login di sini</a>
                </div>
            </div>
        </div>
    </div>
@endsection
