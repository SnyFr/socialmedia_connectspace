<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ConnectSpace</title>

    <!-- Bootstrap 5 CSS & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --bg-main: #09090b;          /* Charcoal Dark khas Laravel */
            --bg-surface: #18181b;       /* Warna card (Zinc 900) */
            --bg-input: #121215;         /* Warna input & box komentar */
            --accent-color: #FF2D20;     /* Merah ikonik Laravel */
            --accent-hover: #dc2626;     /* Merah lebih gelap saat hover */
            --text-main: #f4f4f5;
            --text-muted: #a1a1aa;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            background-color: var(--bg-main);
            color: var(--text-main);
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            min-height: 100vh;
        }

        /* Top Navbar */
        .navbar-custom {
            background-color: rgba(12, 12, 14, 0.92);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .navbar-brand-custom {
            color: var(--accent-color) !important;
            font-weight: 800;
            letter-spacing: -0.5px;
            transition: opacity 0.2s ease;
        }

        .navbar-brand-custom:hover {
            opacity: 0.85;
        }

        /* Hilangkan panah bawaan dropdown pada avatar */
        .avatar-dropdown-toggle::after {
            display: none !important;
        }

        .avatar-trigger {
            padding: 3px;
            border-radius: 50px;
            transition: background-color 0.2s ease;
        }

        .avatar-trigger:hover {
            background-color: rgba(255, 45, 32, 0.12);
        }

        /* Dropdown Menu Dark */
        .dropdown-menu-custom {
            background-color: var(--bg-surface);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 14px;
            min-width: 230px;
        }

        .dropdown-menu-custom .dropdown-item {
            color: var(--text-main);
            border-radius: 8px;
            margin: 0 6px;
            width: calc(100% - 12px);
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .dropdown-menu-custom .dropdown-item:hover {
            background-color: rgba(255, 45, 32, 0.12);
            color: #ffffff;
        }

        .dropdown-menu-custom .dropdown-item.text-danger:hover {
            background-color: rgba(255, 45, 32, 0.18);
            color: #ff554a !important;
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

        .text-info {
            color: var(--accent-color) !important;
        }

        .cursor-pointer {
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- NAVBAR UTAMA (Tampil saat sudah login) -->
    @auth
        <nav class="navbar navbar-dark navbar-custom sticky-top py-2">
            <div class="container" style="max-width: 860px;">
                
                <!-- MARGIN START: Logo Strava + ConnectSpace (Klik ke Feed) -->
                <a href="{{ route('feed.index') }}" class="navbar-brand navbar-brand-custom d-flex align-items-center gap-2 m-0">
                    <i class="bi bi-strava fs-3"></i>
                    <span class="fs-5">ConnectSpace</span>
                </a>

                <!-- MARGIN END: Foto Profil + Dropdown Menu -->
                <div class="dropdown ms-auto">
                    <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle avatar-dropdown-toggle avatar-trigger" 
                       id="userProfileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <span class="d-none d-sm-inline text-light fw-semibold small ps-2">{{ Auth::user()->name }}</span>
                        <img src="{{ Auth::user()->avatar ? asset('storage/' . Auth::user()->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=FF2D20&color=ffffff' }}" 
                             alt="{{ Auth::user()->name }}"
                             class="rounded-circle border border-secondary border-opacity-50" 
                             width="38" height="38" style="width: 38px; height: 38px; object-fit: cover;">
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark dropdown-menu-custom shadow-lg mt-2 py-2" aria-labelledby="userProfileDropdown">
                        <!-- Info Akun -->
                        <li class="px-3 py-2 border-bottom border-secondary border-opacity-25 mb-1">
                            <div class="fw-bold text-white small">{{ Auth::user()->name }}</div>
                            <div class="text-muted-custom text-break" style="font-size: 0.75rem;">{{ Auth::user()->email }}</div>
                        </li>

                        <!-- Profil Saya (Postingan & Post Tersimpan) -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 small fw-semibold" href="{{ route('users.show', Auth::id()) }}">
                                <i class="bi bi-person-circle text-info fs-6"></i>
                                <span>Profil Saya</span>
                            </a>
                        </li>

                        <!-- 1. Edit Profil -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 small fw-semibold" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-gear text-info fs-6"></i>
                                <span>Edit Profil</span>
                            </a>
                        </li>

                        <!-- 2. Ubah Password -->
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2 py-2 small fw-semibold" href="{{ route('profile.edit') }}#ubah-password">
                                <i class="bi bi-shield-lock text-info fs-6"></i>
                                <span>Ubah Password</span>
                            </a>
                        </li>

                        <li><hr class="dropdown-divider border-secondary border-opacity-25 my-1"></li>

                        <!-- 3. Logout -->
                        <li>
                            <button type="button" 
                                    class="dropdown-item d-flex align-items-center gap-2 py-2 small fw-semibold text-danger" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#logoutConfirmModal">
                                <i class="bi bi-box-arrow-right fs-6"></i>
                                <span>Logout</span>
                            </button>
                        </li>
                    </ul>
                </div>

            </div>
        </nav>
    @endauth

    <!-- KONTEN UTAMA -->
    <main class="container py-4" style="max-width: 860px;">
        <!-- Alert Success -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #14532d; color: #dcfce7;">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Alert Error -->
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="background-color: #7f1d1d; color: #fee2e2;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- ALERT CARD MODAL KONFIRMASI HAPUS (Global & Reusable) -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border border-secondary border-opacity-25 shadow-lg rounded-4" style="background-color: #18181b;">
                <div class="modal-body p-4 text-center">
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" 
                         style="width: 56px; height: 56px; background-color: rgba(255, 45, 32, 0.12); color: #FF2D20;">
                        <i class="bi bi-trash3-fill fs-4"></i>
                    </div>

                    <h5 class="fw-bold text-white mb-2">Konfirmasi Hapus</h5>
                    <p id="deleteModalMessage" class="text-muted-custom small mb-4">
                        Yakin ingin menghapus data ini?
                    </p>

                    <form id="deleteConfirmForm" method="POST" class="d-flex gap-2">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-secondary rounded-pill w-50 fw-semibold" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-accent rounded-pill w-50">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ALERT CARD MODAL KONFIRMASI LOGOUT -->
    <div class="modal fade" id="logoutConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" style="max-width: 380px;">
            <div class="modal-content border border-secondary border-opacity-25 shadow-lg rounded-4" style="background-color: #18181b;">
                <div class="modal-body p-4 text-center">
                    <!-- Ikon Logout Merah Laravel -->
                    <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle" 
                         style="width: 56px; height: 56px; background-color: rgba(255, 45, 32, 0.12); color: #FF2D20;">
                        <i class="bi bi-box-arrow-right fs-4"></i>
                    </div>

                    <!-- Judul & Pesan -->
                    <h5 class="fw-bold text-white mb-2">Konfirmasi Logout</h5>
                    <p class="text-muted-custom small mb-4">
                        Yakin ingin keluar dari akun ConnectSpace Anda?
                    </p>

                    <!-- Form Eksekusi Logout (Tetap memakai POST & @csrf) -->
                    <form action="{{ route('logout') }}" method="POST" class="d-flex gap-2 m-0">
                        @csrf
                        <button type="button" class="btn btn-outline-secondary rounded-pill w-50 fw-semibold" data-bs-dismiss="modal">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-accent rounded-pill w-50">
                            Ya, Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Kontrol Alert Card Delete, Auto-Dismiss Alert, & Scroll Restoration -->
    <script>
        function openDeleteModal(deleteUrl, message) {
            const deleteForm = document.getElementById('deleteConfirmForm');
            const modalMessage = document.getElementById('deleteModalMessage');

            deleteForm.action = deleteUrl;
            modalMessage.innerText = message;

            const deleteModal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        }

        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }

        document.addEventListener('submit', function (e) {
            const form = e.target;
            const actionPath = new URL(form.action).pathname;
            const spoofedMethod = form.querySelector('input[name="_method"]')?.value?.toUpperCase();
            const actualMethod = spoofedMethod || form.method.toUpperCase();

            const isNewPost = (actionPath === '/posts' && actualMethod === 'POST');

            if (isNewPost) {
                sessionStorage.removeItem('scrollPosition');
            } else {
                sessionStorage.setItem('scrollPosition', window.scrollY);
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            detectUserLocation();
            // Auto-dismiss alert success dalam 3 detik
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                setTimeout(function () {
                    const bsAlert = bootstrap.Alert.getOrCreateInstance(successAlert);
                    bsAlert.close();
                }, 3000);
            }

            // Kembalikan posisi scroll setelah reload
            const savedScroll = sessionStorage.getItem('scrollPosition');
            if (savedScroll !== null) {
                const targetY = parseInt(savedScroll, 10);
                sessionStorage.removeItem('scrollPosition');

                window.scrollTo({ top: targetY, behavior: 'instant' });

                window.addEventListener('load', function () {
                    window.scrollTo({ top: targetY, behavior: 'instant' });
                }, { once: true });
            }
        });

        // 1. Validasi Live Max 500 Karakter & Disable Tombol Posting
        function validatePostLength(textarea, counterId, warningId, submitBtnId) {
            const maxChars = 500;
            const currentLength = textarea.value.length;
            const counter = document.getElementById(counterId);
            const warning = document.getElementById(warningId);
            const submitBtn = document.getElementById(submitBtnId);

            if (counter) {
                counter.innerText = `${currentLength} / ${maxChars}`;
            }

            if (currentLength > maxChars) {
                textarea.classList.add('border-danger');
                if (counter) {
                    counter.classList.remove('text-muted-custom');
                    counter.classList.add('text-danger', 'fw-bold');
                }
                if (warning) warning.classList.remove('d-none');
                if (submitBtn) submitBtn.disabled = true;
            } else {
                textarea.classList.remove('border-danger');
                if (counter) {
                    counter.classList.add('text-muted-custom');
                    counter.classList.remove('text-danger', 'fw-bold');
                }
                if (warning) warning.classList.add('d-none');
                if (submitBtn) submitBtn.disabled = false;
            }
        }

        // 2. Preview Gambar Post Status Baru
        function previewPostImage(event) {
            const input = event.target;
            const container = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('postImagePreview');
            const fileName = document.getElementById('fileName');

            if (input.files && input.files[0]) {
                preview.src = URL.createObjectURL(input.files[0]);
                container.classList.remove('d-none');
                if (fileName) fileName.innerText = input.files[0].name;
            }
        }

        function removePostImage() {
            const input = document.getElementById('postImageInput');
            const container = document.getElementById('imagePreviewContainer');
            const preview = document.getElementById('postImagePreview');
            const fileName = document.getElementById('fileName');

            input.value = '';
            preview.src = '';
            container.classList.add('d-none');
            if (fileName) fileName.innerText = '';
        }

        // 3. Toggle Input Lokasi Komentar
        function applyDetectedLocation(locationText) {
            if (!locationText) return;
            document.querySelectorAll('.auto-comment-location').forEach(function (input) {
                input.value = locationText;
            });
        }

        const LOCAL_CITIES = [
            { name: 'Jakarta, Indonesia',    minLat: -6.375, maxLat: -6.080, minLng: 106.680, maxLng: 106.975 },
            { name: 'Depok, Indonesia',      minLat: -6.465, maxLat: -6.350, minLng: 106.730, maxLng: 106.910 },
            { name: 'Tangerang, Indonesia',  minLat: -6.360, maxLat: -6.080, minLng: 106.520, maxLng: 106.750 },
            { name: 'Bekasi, Indonesia',     minLat: -6.430, maxLat: -6.150, minLng: 106.930, maxLng: 107.180 },
            { name: 'Bogor, Indonesia',      minLat: -6.680, maxLat: -6.500, minLng: 106.730, maxLng: 106.860 },
            { name: 'Bandung, Indonesia',    minLat: -6.970, maxLat: -6.840, minLng: 107.540, maxLng: 107.740 },
            { name: 'Semarang, Indonesia',   minLat: -7.120, maxLat: -6.930, minLng: 110.270, maxLng: 110.500 },
            { name: 'Yogyakarta, Indonesia', minLat: -7.840, maxLat: -7.760, minLng: 110.340, maxLng: 110.410 },
            { name: 'Surabaya, Indonesia',   minLat: -7.360, maxLat: -7.190, minLng: 112.600, maxLng: 112.830 },
            { name: 'Medan, Indonesia',      minLat: 3.450,  maxLat: 3.790,  minLng: 98.560,  maxLng: 98.740  },
            { name: 'Denpasar, Indonesia',   minLat: -8.750, maxLat: -8.600, minLng: 115.170, maxLng: 115.270 }
        ];

        function resolveLocationFromCoords(lat, lng) {
            // 1. Cek apakah latitude & longitude masuk dalam batas area kota
            const matchedCity = LOCAL_CITIES.find(city =>
                lat >= city.minLat && lat <= city.maxLat &&
                lng >= city.minLng && lng <= city.maxLng
            );

            if (matchedCity) {
                return matchedCity.name;
            }

            // 2. Jika di luar daftar kota di atas, tampilkan titik koordinat (4 angka di belakang koma)
            return `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
        }

        function detectUserLocation() {
            // Bersihkan cache dari percobaan API sebelumnya
            sessionStorage.removeItem('user_gps_location');
            sessionStorage.removeItem('user_gps_location_v2');
            sessionStorage.removeItem('user_detected_location');

            const cachedCoordsLocation = sessionStorage.getItem('user_offline_coords_location');
            if (cachedCoordsLocation) {
                applyDetectedLocation(cachedCoordsLocation);
                return;
            }

            if (!('geolocation' in navigator)) {
                console.warn('Browser tidak mendukung Geolocation.');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    const { latitude, longitude } = position.coords;
                    const locationText = resolveLocationFromCoords(latitude, longitude);

                    sessionStorage.setItem('user_offline_coords_location', locationText);
                    applyDetectedLocation(locationText);
                },
                function (error) {
                    console.warn('Akses lokasi ditolak atau gagal:', error.message);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        }
        
        // 4. Preview Gambar Komentar
        function previewCommentImage(event, uniqueId) {
            const input = event.target;
            const container = document.getElementById(`commentPreviewContainer-${uniqueId}`);
            const preview = document.getElementById(`commentPreviewImg-${uniqueId}`);

            if (input.files && input.files[0]) {
                preview.src = URL.createObjectURL(input.files[0]);
                container.classList.remove('d-none');
            }
        }

        function removeCommentImage(uniqueId) {
            const input = document.getElementById(`commentImageInput-${uniqueId}`);
            const container = document.getElementById(`commentPreviewContainer-${uniqueId}`);
            const preview = document.getElementById(`commentPreviewImg-${uniqueId}`);

            input.value = '';
            preview.src = '';
            container.classList.add('d-none');
        }

        // Toggle Form Reply Komentar + Auto-Mention jika membalas sesama balasan
        // Toggle Buka / Tutup Daftar Balasan ("Lihat balasan (X)" <-> "Sembunyikan balasan")
        function toggleReplies(uniqueKey, forceOpen = false) {
            const container = document.getElementById(`repliesContainer-${uniqueKey}`);
            const btn = document.getElementById(`toggleRepliesBtn-${uniqueKey}`);
            const textEl = document.getElementById(`toggleRepliesText-${uniqueKey}`);
            const iconEl = document.getElementById(`toggleRepliesIcon-${uniqueKey}`);

            if (!container || !btn) return;

            const count = btn.getAttribute('data-count');
            const shouldOpen = forceOpen ? true : container.classList.contains('d-none');

            if (shouldOpen) {
                container.classList.remove('d-none');
                if (textEl) textEl.innerText = 'Sembunyikan balasan';
                if (iconEl) iconEl.className = 'bi bi-chevron-up';
            } else {
                container.classList.add('d-none');
                if (textEl) textEl.innerText = `Lihat balasan (${count})`;
                if (iconEl) iconEl.className = 'bi bi-chevron-down';
            }
        }

        // Toggle Form Reply Komentar
        function toggleReplyForm(uniqueKey) {
            const container = document.getElementById(`replyFormContainer-${uniqueKey}`);
            const input = document.getElementById(`replyInput-${uniqueKey}`);

            if (!container || !input) return;

            container.classList.toggle('d-none');
            if (!container.classList.contains('d-none')) {
                input.focus();
            }
        }

        // Simpan jejak thread yang sedang dibalas agar otomatis terbuka setelah reload
        document.addEventListener('submit', function (e) {
            if (e.target.classList.contains('nested-reply-form')) {
                const openKeys = [];
                let currentWrapper = e.target.closest('.replies-wrapper');

                // Telusuri seluruh induk thread di atasnya
                while (currentWrapper) {
                    const key = currentWrapper.id.replace('repliesContainer-', '');
                    openKeys.push(key);
                    currentWrapper = currentWrapper.parentElement.closest('.replies-wrapper');
                }

                // Tambahkan key komentar yang baru saja dibalas
                const targetKey = e.target.getAttribute('data-parent-key');
                if (targetKey) openKeys.push(targetKey);

                sessionStorage.setItem('autoOpenReplyThreads', JSON.stringify(openKeys));
            }
        });

        // Buka kembali thread balasan secara otomatis setelah halaman reload
        document.addEventListener('DOMContentLoaded', function () {
            const savedThreads = sessionStorage.getItem('autoOpenReplyThreads');
            if (savedThreads) {
                sessionStorage.removeItem('autoOpenReplyThreads');
                try {
                    const keys = JSON.parse(savedThreads);
                    keys.forEach(function (key) {
                        toggleReplies(key, true);
                    });
                } catch (err) {
                    console.warn(err);
                }
            }
        });
    </script>
</body>
</html>