<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConnectSpace - Media Sosial Internal</title>

    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Custom Styling Theme (X / Threads Inspired - Slate & Dark Mode) -->
    <style>
        :root {
            --bg-body: #0f172a;
            /* Background utama: Deep Dark Slate */
            --bg-card: #1e293b;
            /* Background card/post: Dark Slate */
            --bg-card-hover: #334155;
            /* Warna hover item */
            --border-color: #334155;
            /* Warna border tipis */
            --text-main: #f8fafc;
            /* Warna teks utama (Off-White) */
            --text-muted: #94a3b8;
            /* Warna teks sekunder (Slate Gray) */
            --accent-color: #38bdf8;
            /* Warna aksen utama (Sky Blue / X Accent) */
            --accent-hover: #0284c7;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
        }

        /* Custom Cards & Navbar */
        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 16px;
        }

        .card-header,
        .card-footer {
            background-color: transparent;
            border-color: var(--border-color);
        }

        .form-control {
            background-color: #0f172a;
            border: 1px solid var(--border-color);
            color: var(--text-main);
            border-radius: 10px;
        }

        .form-control:focus {
            background-color: #0f172a;
            color: var(--text-main);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.25rem rgba(56, 189, 248, 0.25);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        /* Sidebar Nav Styles */
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            border-right: 1px solid var(--border-color);
            padding: 1.5rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--accent-color);
            text-decoration: none;
            padding: 0.5rem 0.5rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .nav-link-custom {
            color: var(--text-main);
            font-size: 1rem;
            font-weight: 600;
            padding: 0.75rem 1rem;
            border-radius: 30px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            transition: background 0.2s ease;
            margin-bottom: 0.35rem;
            white-space: nowrap;
        }

        .nav-link-custom:hover,
        .nav-link-custom.active {
            background-color: var(--bg-card-hover);
            color: var(--accent-color);
        }

        .btn-accent {
            background-color: var(--accent-color);
            color: #0f172a;
            font-weight: 700;
            border-radius: 30px;
            border: none;
            padding: 0.6rem 1.2rem;
        }

        .btn-accent:hover {
            background-color: var(--accent-hover);
            color: #ffffff;
        }

        .text-muted-custom {
            color: var(--text-muted) !important;
        }

        @media (min-width: 768px) and (max-width: 1199.98px) {
            .sidebar-brand {
                font-size: 1.1rem;
                gap: 0.4rem;
                padding: 0.5rem 0.25rem;
            }

            .nav-link-custom {
                font-size: 0.95rem;
                padding: 0.6rem 0.75rem;
                gap: 0.5rem;
            }
        }
    </style>
</head>

<body>

    @auth
        <!-- LAYOUT TERPISAH DENGAN SIDEBAR UNTUK USER LOGIN (X/Threads Style) -->
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar Kiri -->
                <div class="col-md-3 col-lg-3 col-xl-2 p-0 d-none d-md-block">
                    <aside class="sidebar">
                        <div>
                            <!-- Brand / Logo -->
                            <a href="{{ route('feed.index') }}" class="sidebar-brand">
                                <i class="bi bi-strava"></i>
                                <span>ConnectSpace</span>
                            </a>

                            <!-- Menu Navigasi -->
                            <nav>
                                <a href="{{ route('feed.index') }}"
                                    class="nav-link-custom {{ request()->routeIs('feed.index') ? 'active' : '' }}">
                                    <i class="bi bi-house-door-fill fs-4"></i>
                                    <span>Beranda</span>
                                </a>
                                <a href="{{ route('profile.edit') }}"
                                    class="nav-link-custom {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                                    <i class="bi bi-person-fill fs-4"></i>
                                    <span>Profil Saya</span>
                                </a>
                            </nav>
                        </div>

                        <!-- Profil User & Tombol Logout di Bawah Sidebar -->
                        <div class="pt-3 border-top border-secondary">
                            <div class="d-flex align-items-center justify-content-between mb-3 px-2">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=38bdf8&color=0f172a' }}"
                                        class="rounded-circle border border-secondary" width="40" height="40"
                                        style="object-fit: cover;" alt="Avatar">
                                    <div class="lh-1">
                                        <div class="fw-bold text-white mb-1">{{ Auth::user()->name }}</div>
                                        <small class="text-muted-custom text-break">{{ Auth::user()->email }}</small>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100 rounded-pill btn-sm fw-bold">
                                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                                </button>
                            </form>
                        </div>
                    </aside>
                </div>

                <!-- TOPBAR NAVIGATION (Khusus Mode Mobile / Responsive) -->
                <nav class="navbar navbar-dark d-md-none border-bottom border-secondary border-opacity-25 px-3 py-2 sticky-top"
                    style="background-color: #0b1120;">
                    <div class="container-fluid p-0 d-flex justify-content-between align-items-center">

                        <!-- 1. Judul & Logo ConnectSpace (Bisa Diklik -> Ke Feed Beranda) -->
                        <a href="{{ route('feed.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                            <i class="bi bi-strava text-info fs-4"></i>
                            <span class="fw-bold text-info fs-5 mb-0" style="letter-spacing: -0.5px;">ConnectSpace</span>
                        </a>

                        <!-- Menu Kanan: Foto Profil & Tombol Logout -->
                        <div class="d-flex align-items-center gap-2">

                            <!-- 2. Avatar Profil (Bisa Diklik -> Ke Edit Profil) -->
                            <a href="{{ route('profile.edit') }}" title="Profil Saya"
                                class="d-flex align-items-center text-decoration-none">
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=38bdf8&color=0f172a' }}"
                                    class="rounded-circle border border-secondary border-opacity-25" width="35"
                                    height="35" style="object-fit: cover;">
                            </a>

                            <!-- 3. Tombol Logout (Bulat Presisi & Sejajar) -->
                            <form action="{{ route('logout') }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit"
                                    class="btn btn-sm btn-outline-danger d-flex align-items-center justify-content-center p-0 rounded-circle"
                                    style="width: 35px; height: 35px;" title="Logout">
                                    <i class="bi bi-box-arrow-right fs-6"></i>
                                </button>
                            </form>
                        </div>

                    </div>
                </nav>

                <!-- Area Konten Utama (Feed / Profile Edit) -->
                <main class="col-md-9 col-lg-9 col-xl-8 py-4 px-md-4">
                    <!-- Alert Notifikasi Sukses -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show bg-success text-white border-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Alert Notifikasi Error -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show bg-danger text-white border-0 shadow-sm"
                            role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Tempat Konten disuntikkan -->
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <!-- LAYOUT TERPUSAT UNTUK TAMU / LOGIN / REGISTER -->
        <main class="py-5">
            <div class="container">
                <!-- Alert Notifikasi -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show bg-success text-white border-0 shadow-sm"
                        role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    @endauth

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cari elemen alert success di halaman
            const successAlert = document.querySelector('.alert-success');

            if (successAlert) {
                // JALANKAN TIMEOUT (3000ms = 3 detik)
                setTimeout(function() {
                    // Gunakan API Bootstrap Alert untuk menutup alert secara halus
                    const alert = bootstrap.Alert.getOrCreateInstance(successAlert);
                    alert.close();
                }, 3000);
            }
        });

        // Simpan posisi scroll ke sessionStorage saat pengguna submit form apa pun
        document.addEventListener('submit', function() {
            sessionStorage.setItem('scrollPosition', window.scrollY);
        });

        // Saat halaman selesai di-reload, kembalikan posisi scroll ke koordinat semula
        document.addEventListener('DOMContentLoaded', function() {
            const scrollPosition = sessionStorage.getItem('scrollPosition');
            if (scrollPosition !== null) {
                window.scrollTo({
                    top: parseInt(scrollPosition),
                    behavior: 'instant' // Memindahkan layar secara instan tanpa animasi mengambang
                });
                sessionStorage.removeItem('scrollPosition'); // Hapus memori setelah digunakan
            }
        });
    </script>
</body>

</html>
