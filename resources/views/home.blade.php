<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SMK YADIKA 13 - Sistem Informasi Akademik</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="%23ffffff" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="%23ffffff" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="%23ffffff" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.1;
        }
        .hero-content {
            position: relative;
            z-index: 2;
        }
        .school-logo {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        .school-logo i {
            font-size: 3rem;
            color: white;
        }
        .btn-login {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        .btn-login:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        .features-section {
            padding: 80px 0;
            background: #f8f9fa;
        }
        .feature-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
        }
        .feature-card:hover {
            transform: translateY(-10px);
        }
        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 2rem;
        }
        .about-section {
            padding: 80px 0;
            background: white;
        }
        .stats-section {
            padding: 60px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stat-item {
            text-align: center;
            margin-bottom: 30px;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        .footer {
            background: #343a40;
            color: white;
            padding: 40px 0;
            margin-top: 0;
        }
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        .shape:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }
        .shape:nth-child(2) {
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }
        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('bg/logo.png') }}" width="50" height="50" class="me-2">
                SMK YADIKA 13
            </a> 
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Fitur</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn-login ms-3" href="{{ route('login') }}">
                            <i class="fas fa-sign-in-alt me-2"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section">
        <div class="floating-shapes">
            <div class="shape"><i class="fas fa-graduation-cap fa-3x"></i></div>
            <div class="shape"><i class="fas fa-book fa-3x"></i></div>
            <div class="shape"><i class="fas fa-users fa-3x"></i></div>
        </div>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center hero-content">
                    <div class="school-logo">
                        <img src="{{ asset('bg/logo.png') }}" alt="Logo SMK YADIKA 13" style="width:150px; height:150px; object-fit:contain;">                   </div>
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Selamat Datang di<br>
                        <span class="text-warning">SMK YADIKA 13</span>
                    </h1>
                    <p class="lead text-white-50 mb-5 fs-5">
                        Sistem Informasi Akademik Terintegrasi untuk mendukung proses pembelajaran yang efektif dan efisien di SMK Yadika 13.
                    </p>
                    <a href="{{ route('login') }}" class="btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Masuk ke Sistem
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold mb-4">Tentang SMK Yadika 13</h2>
                    <p class="lead text-muted mb-5">
                        SMK Yadika 13 adalah lembaga pendidikan kejuruan yang berkomitmen untuk menghasilkan lulusan yang kompeten,
                        berakhlak mulia, dan siap menghadapi tantangan dunia kerja. Dengan didukung oleh sistem informasi akademik
                        modern, kami menyediakan lingkungan pembelajaran yang kondusif dan terintegrasi.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h5 class="fw-bold">Pendidik Berkualitas</h5>
                        <p class="text-muted">Guru-guru profesional dan berpengalaman dalam bidangnya masing-masing.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h5 class="fw-bold">Fasilitas Modern</h5>
                        <p class="text-muted">Laboratorium dan workshop yang dilengkapi dengan peralatan terkini.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h5 class="fw-bold">Kerjasama Industri</h5>
                        <p class="text-muted">Bermitra dengan berbagai perusahaan untuk program magang dan penempatan kerja.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="display-5 fw-bold mb-4">Fitur Sistem Informasi Akademik</h2>
                    <p class="lead text-muted mb-5">
                        Platform terintegrasi yang memudahkan pengelolaan proses akademik untuk semua pengguna.
                    </p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h5 class="fw-bold">Dashboard Admin</h5>
                        <p class="text-muted">Kelola pengguna, mata pelajaran, jadwal, dan data master lainnya.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                        <h5 class="fw-bold">Portal Guru</h5>
                        <p class="text-muted">Kelola materi pembelajaran, tugas, ujian, dan absensi siswa.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="fw-bold">Portal Siswa</h5>
                        <p class="text-muted">Akses materi, kerjakan tugas, lihat nilai, dan pantau kehadiran.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h5 class="fw-bold">Laporan & Analitik</h5>
                        <p class="text-muted">Monitoring performa akademik dan statistik pembelajaran.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">500+</div>
                        <div>Siswa Aktif</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">50+</div>
                        <div>Guru Berkompeten</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">5+</div>
                        <div>Program Keahlian</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-item">
                        <div class="stat-number">95%</div>
                        <div>Tingkat Kelulusan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">
                        <i class="fas fa-school me-2"></i>SMK YADIKA 13
                    </h5>
                    <p class="text-white">
                        Membentuk generasi muda yang kompeten, inovatif, dan berakhlak mulia
                        melalui pendidikan kejuruan berkualitas.
                    </p>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">Kontak</h5>
                    <ul class="list-unstyled text-white">
                        <li><i class="fas fa-map-marker-alt me-2"></i>Jalan Raya Villa I, RT.01/RW.01, Jejalenjaya, Kec. Tambun Utara, Kabupaten Bekasi, Jawa Barat 17510</li>
                        <li><i class="fas fa-phone me-2"></i>0812-1934-9338</li>
                        <li><i class="fas fa-envelope me-2"></i>https://smkyadika13.sch.id</li>
                    </ul>
                </div>
                <div class="col-lg-4 mb-4">
                    <h5 class="fw-bold mb-3">Sosial Media</h5>
                    <ul class="list-unstyled">
                        <li><a href="https://www.instagram.com/smk_yadika_13/" class="text-white text-decoration-none" target="_blank"><i class="fab fa-instagram me-2"></i>Instagram</a></li>
                        <li><a href="https://www.youtube.com/@SMKYADIKA13" class="text-white text-decoration-none" target="_blank"><i class="fab fa-youtube me-2"></i>YouTube</a></li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0 text-white">
                    &copy; 2024 SMK YADIKA 13. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Smooth scrolling -->
    <script>
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            });
        });
    </script>
</body>
</html>
