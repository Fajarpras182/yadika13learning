<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-Learning SMK Yadika 13')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    @push('styles')
    <style>
    /* Sidebar collapsible menu styles */
    .sidebar .nav-link[data-bs-toggle="collapse"] {
        position: relative;
    }

    .sidebar .nav-link[data-bs-toggle="collapse"] .fa-chevron-down {
        transition: transform 0.3s ease;
        font-size: 0.8em;
    }

    .sidebar .nav-link[data-bs-toggle="collapse"][aria-expanded="true"] .fa-chevron-down {
        transform: rotate(180deg);
    }

    .sidebar .collapse ul.nav {
        border-left: 2px solid rgba(255, 255, 255, 0.1);
        margin-top: 0.25rem;
    }

    .sidebar .collapse .nav-link {
        padding: 0.5rem 1rem 0.5rem 2rem;
        font-size: 0.9em;
    }

    .sidebar .collapse .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    .sidebar .collapse .nav-link.active {
        background-color: rgba(255, 255, 255, 0.2);
        border-left: 3px solid #fff;
        padding-left: 1.75rem;
    }
    </style>
    @endpush
    @stack('styles')
</head>
<body>
    @auth
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-logo">
            <h4>
                <i class="fas fa-graduation-cap"></i>
                E-Learning
            </h4>
            <p>SMK Yadika 13</p>
        </div>

        <ul class="nav flex-column">
            @if(auth()->user()->isAdmin())
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <!-- Manajemen User -->
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#userManagement" aria-expanded="false">
                        <i class="fas fa-users-cog"></i> Manajemen User
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="collapse" id="userManagement">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.teachers*') ? 'active' : '' }}" href="{{ route('admin.teachers.index') }}">
                                    <i class="fas fa-chalkboard-teacher"></i> Kelola Guru
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.students*') ? 'active' : '' }}" href="{{ route('admin.students.index') }}">
                                    <i class="fas fa-user-graduate"></i> Kelola Siswa
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}" href="{{ route('admin.users') }}">
                                    <i class="fas fa-user-plus"></i> Tambah / Hapus Akun
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Data Master -->
                <li class="nav-item">
                    <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#dataMaster" aria-expanded="false">
                        <i class="fas fa-database"></i> Data Master
                        <i class="fas fa-chevron-down float-end"></i>
                    </a>
                    <div class="collapse" id="dataMaster">
                        <ul class="nav flex-column ms-3">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.courses*') ? 'active' : '' }}" href="{{ route('admin.courses') }}">
                                    <i class="fas fa-book-open"></i> Mata Pelajaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.majors*') ? 'active' : '' }}" href="{{ route('admin.majors.index') }}">
                                    <i class="fas fa-layer-group"></i> Jurusan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.classes*') ? 'active' : '' }}" href="{{ route('admin.classes.index') }}">
                                    <i class="fas fa-school"></i> Kelas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.schedules*') ? 'active' : '' }}" href="{{ route('admin.schedules.index') }}">
                                    <i class="fas fa-calendar-alt"></i> Jadwal Pelajaran
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Manajemen Ujian -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.ujian*') ? 'active' : '' }}" href="{{ route('admin.ujian') }}">
                        <i class="fas fa-clipboard-list"></i> Manajemen Ujian
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.sesi-ujian*') ? 'active' : '' }}" href="{{ route('admin.sesi-ujian') }}">
                        <i class="fas fa-clock"></i> Manajemen Sesi Ujian
                    </a>
                </li>

                <!-- Profil Admin -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.profile') ? 'active' : '' }}" href="{{ route('admin.profile') }}">
                        <i class="fas fa-user-edit"></i> Profil Admin
                    </a>
                </li>
            @elseif(auth()->user()->isGuru())
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.dashboard') ? 'active' : '' }}" href="{{ route('guru.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <!-- Kelas Saya -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.courses*') ? 'active' : '' }}" href="{{ route('guru.courses') }}">
                        <i class="fas fa-chalkboard"></i> Kelas Saya
                    </a>
                </li>





                <!-- Penilaian / Nilai -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.reports') ? 'active' : '' }}" href="{{ route('guru.reports') }}">
                        <i class="fas fa-chart-line"></i> Penilaian / Nilai
                    </a>
                </li>

                <!-- Rekap Absensi -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.attendance-reports') ? 'active' : '' }}" href="{{ route('guru.attendance-reports') }}">
                        <i class="fas fa-calendar-check"></i> Rekap Absensi
                    </a>
                </li>

                <!-- Manajemen Bank Soal -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.bank-soal*') ? 'active' : '' }}" href="{{ route('guru.bank-soal') }}">
                        <i class="fas fa-database"></i> Manajemen Bank Soal
                    </a>
                </li>

                <!-- Nilai Ujian -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.nilai-ujian') ? 'active' : '' }}" href="{{ route('guru.nilai-ujian') }}">
                        <i class="fas fa-chart-line"></i> Nilai Ujian
                    </a>
                </li>







                <!-- Forum Diskusi Kelas -->

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.messages') ? 'active' : '' }}" href="{{ route('guru.messages') }}">
                        <i class="fas fa-comments"></i> Forum Diskusi Kelas
                    </a>
                </li>





                <!-- Pengaturan Profil Guru -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guru.profile') ? 'active' : '' }}" href="{{ route('guru.profile') }}">
                        <i class="fas fa-user-edit"></i> Profil Guru
                    </a>
                </li>
            @elseif(auth()->user()->isSiswa())
                <!-- Dashboard Siswa -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.dashboard') ? 'active' : '' }}" href="{{ route('siswa.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>

                <!-- Kelas Saya -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.courses*') ? 'active' : '' }}" href="{{ route('siswa.courses') }}">
                        <i class="fas fa-chalkboard"></i> Kelas Saya
                    </a>
                </li>

                <!-- Nilai -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.grades*') ? 'active' : '' }}" href="{{ route('siswa.grades') }}">
                        <i class="fas fa-chart-line"></i> Nilai
                    </a>
                </li>

                <!-- Presensi -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.attendances*') ? 'active' : '' }}" href="{{ route('siswa.attendances') }}">
                        <i class="fas fa-calendar-check"></i> Presensi
                    </a>
                </li>

                <!-- Forum Diskusi Kelas -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.forum') ? 'active' : '' }}" href="{{ route('siswa.forum') }}">
                        <i class="fas fa-comments"></i> Forum Diskusi Kelas
                    </a>
                </li>

                <!-- Ujian -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.ujian') ? 'active' : '' }}" href="{{ route('siswa.ujian') }}">
                        <i class="fas fa-clipboard-list"></i> Ujian
                    </a>
                </li>

                <!-- Pengaturan Profil Siswa -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('siswa.profile*') ? 'active' : '' }}" href="{{ route('siswa.profile') }}">
                        <i class="fas fa-user-edit"></i> Profil Siswa
                    </a>
                </li>
            @endif
        </ul>
    </nav>

    <!-- Navbar Top -->
    <div class="navbar-top">
        <div class="navbar-left">
            <!-- Hamburger Menu Button (Mobile Only) -->
            <button class="hamburger-btn d-md-none" id="sidebarToggle" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h1>@yield('page-title', 'Dashboard')</h1>
        </div>
        <div class="user-menu">
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div>
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <span class="user-role">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Flash messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Page content -->
        @yield('content')
    </main>
    @else
        @yield('content')
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JS for Sidebar Toggle -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');

            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                // Close sidebar when clicking outside on mobile
                document.addEventListener('click', function(event) {
                    if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
