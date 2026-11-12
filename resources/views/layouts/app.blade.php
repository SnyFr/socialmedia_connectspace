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
            --bg-main: #09090b;
            /* Hitam charcoal khas Laravel Welcome Dark */
            --bg-surface: #18181b;
            /* Warna card (Zinc 900) */
            --bg-input: #121215;
            /* Warna input & box komentar */
            --accent-color: #FF2D20;
            /* Merah ikonik Laravel */
            --accent-hover: #dc2626;
            /* Merah lebih gelap saat hover */
            --text-main: #f4f4f5;
            --text-muted: #a1a1aa;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
        }

        /* Sidebar & Topbar */
        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            padding: 1.5rem 1rem;
            border-right: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-color: #0c0c0e;
        }

        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--accent-color);
            text-decoration: none;
            padding: 0.5rem;
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
            transition: all 0.2s ease;
            margin-bottom: 0.35rem;
            white-space: nowrap;
        }

        .nav-link-custom:hover {
            background-color: rgba(255, 45, 32, 0.1);
            color: var(--accent-color);
        }

        .nav-link-custom.active {
            background-color: rgba(255, 45, 32, 0.15);
            color: var(--accent-color);
        }

        /* Card & Form Surface */
        .card {
            background-color: var(--bg-surface);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
        }

        .card-header,
        .card-footer {
            background-color: transparent !important;
        }

        .form-control {
            background-color: var(--bg-input);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: var(--text-main);
        }

        .form-control:focus {
            background-color: var(--bg-input);
            color: var(--text-main);
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 45, 32, 0.25);
        }

        .form-control::placeholder {
            color: #71717a;
        }

        /* Tombol Utama (Laravel Red) */
        .btn-accent {
            background-color: var(--accent-color);
            color: #ffffff;
            font-weight: 700;
            border: none;
            border-radius: 25px;
            transition: background-color 0.2s ease;
        }

        .btn-accent:hover,
        .btn-accent:focus {
            background-color: var(--accent-hover);
            color: #ffffff;
        }

        .text-muted-custom {
            color: var(--text-muted) !important;
        }

        /* Override Otomatis Class Biru (text-info & inline dark blue) Tanpa Edit File Lain */
        .text-info {
            color: var(--accent-color) !important;
        }

        [style*="background-color: #0b1120"] {
            background-color: #0c0c0e !important;
        }

        [style*="background-color: #0f172a"],
        [style*="background-color: #090d16"] {
            background-color: var(--bg-input) !important;
        }

        /* Responsif Tablet / Laptop Kecil */
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
        <!-- LAYOUT TERPISAH DENGAN SIDEBAR UNTUK USER LOGIN  -->
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
                                    <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FF2D20&color=ffffff' }}"
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
                                <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FF2D20&color=ffffff' }}"
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
    // 1. Matikan scroll restoration bawaan browser agar tidak loncat/flicker saat reload
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        // 2. Tangkap posisi scroll persis saat form disubmit
        document.addEventListener('submit', function (e) {
            const form = e.target;
            const actionPath = new URL(form.action).pathname;
            const spoofedMethod = form.querySelector('input[name="_method"]')?.value?.toUpperCase();
            const actualMethod = spoofedMethod || form.method.toUpperCase();

            // Cek apakah aksi ini adalah membuat POSTINGAN BARU (POST murni ke /posts)
            const isNewPost = (actionPath === '/posts' && actualMethod === 'POST');

            if (isNewPost) {
                // Jika posting status baru, biarkan layar kembali ke paling atas
                sessionStorage.removeItem('scrollPosition');
            } else {
                // Untuk Like, Komentar, Edit, dan Hapus: simpan titik scroll layar saat ini
                sessionStorage.setItem('scrollPosition', window.scrollY);
            }
        });

        // 3. Kembalikan posisi scroll secara instan tanpa mengarahkan ke card tertentu
        document.addEventListener('DOMContentLoaded', function () {
            const savedScroll = sessionStorage.getItem('scrollPosition');
            if (savedScroll !== null) {
                const targetY = parseInt(savedScroll, 10);
                sessionStorage.removeItem('scrollPosition'); // Langsung bersihkan agar tidak terbawa ke halaman lain

                // Pindahkan layar langsung ke koordinat semula sebelum user melihat kedipan
                window.scrollTo({ top: targetY, behavior: 'instant' });

                // Kunci ulang setelah gambar selesai dimuat agar tinggi gambar tidak menggeser posisi scroll
                window.addEventListener('load', function () {
                    window.scrollTo({ top: targetY, behavior: 'instant' });
                }, { once: true });
            }
        });
    </script>
</body>

</html>
