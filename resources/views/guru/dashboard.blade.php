@extends('layouts.app')

@section('title', 'Dashboard Guru - E-Learning SMK Yadika 13')
@section('page-title', 'Dashboard Guru')

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-item">
        <i class="fas fa-book stat-icon"></i>
        <span class="stat-value">{{ $totalCourses }}</span>
        <span class="stat-label">Mata Pelajaran</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-file-alt stat-icon"></i>
        <span class="stat-value">{{ $totalLessons }}</span>
        <span class="stat-label">Total Materi</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-tasks stat-icon"></i>
        <span class="stat-value">{{ $totalAssignments }}</span>
        <span class="stat-label">Total Tugas</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-user-graduate stat-icon"></i>
        <span class="stat-value">{{ $recentCourses->sum(function($course) { return $course->students->count(); }) }}</span>
        <span class="stat-label">Total Siswa</span>
    </div>
</div>

<div class="row">
    <!-- Today's Schedule -->
    <div class="col-lg-6 mb-4">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-day me-2"></i>Jadwal Hari Ini
                </h6>
            </div>
            <div class="card-body">
                @if($todaySchedule->count() > 0)
                    @foreach($todaySchedule as $schedule)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $schedule->course->nama_mata_pelajaran }}</h6>
                            <small class="text-muted">
                                {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                <br>{{ $schedule->schoolClass->name }} - {{ $schedule->schoolClass->major->name }}
                            </small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success">{{ $schedule->room }}</span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted text-center">Tidak ada jadwal hari ini</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow-md">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('guru.courses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Mata Pelajaran
                    </a>
                    <a href="{{ route('guru.courses') }}" class="btn btn-outline-primary">
                        <i class="fas fa-book me-2"></i>Kelola Mata Pelajaran
                    </a>
                    <a href="{{ route('guru.messages') }}" class="btn btn-outline-info">
                        <i class="fas fa-comments me-2"></i>Pesan
                        @if($unreadMessages > 0)
                            <span class="badge bg-danger">{{ $unreadMessages }}</span>
                        @endif
                    </a>
                    <a href="{{ route('guru.reports') }}" class="btn btn-outline-success">
                        <i class="fas fa-file-alt me-2"></i>Laporan
                    </a>
                    <a href="{{ route('guru.analytics') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-2"></i>Analytics
                    </a>
                    <a href="{{ route('guru.profile') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-user me-2"></i>Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Exam Analytics Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-md">
            <div class="card-header py-3 bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chart-bar me-2"></i>Statistik Ujian & Penilaian
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 text-center mb-3">
                        <div class="h5 text-primary">{{ $examAnalytics['total_exams'] }}</div>
                        <small class="text-muted">Total Ujian</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="h5 text-info">{{ $examAnalytics['total_exams_taken'] }}</div>
                        <small class="text-muted">Ujian Dikerjakan</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="h5 text-success">{{ $examAnalytics['average_score'] }}</div>
                        <small class="text-muted">Rata-rata Skor</small>
                    </div>
                    <div class="col-md-3 text-center mb-3">
                        <div class="h5 text-warning">{{ $pendingGrades }}</div>
                        <small class="text-muted">Tugas Belum Dinilai</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Real-Time Exam Monitoring -->
@if($ongoingExamSessions->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-md border-left-warning">
            <div class="card-header py-3 bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-tv me-2"></i>Ujian Sedang Berlangsung (Live Monitoring)
                </h6>
            </div>
            <div class="card-body">
                @foreach($ongoingExamSessions as $session)
                <div class="mb-4 pb-4 border-bottom">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h6 class="mb-1">
                                <i class="fas fa-hourglass-half me-2 text-warning"></i>
                                {{ $session->ujian->judul }}
                            </h6>
                            <small class="text-muted">
                                Sesi: {{ $session->nama_sesi }} | Mata Pelajaran: {{ $session->ujian->course->nama_mata_pelajaran }}
                            </small>
                        </div>
                        <a href="{{ route('guru.sesi-ujian.show', $session->id) }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-eye me-1"></i>Monitor Live
                        </a>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h5 text-primary mb-1">{{ $session->students->count() }}</div>
                                <small class="text-muted">Siswa Terdaftar</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h5 text-success mb-1"><i class="fas fa-circle text-success me-1"></i>{{ $session->students->where('status', 'aktif')->count() ?? 0 }}</div>
                                <small class="text-muted">Siswa Aktif</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h5 text-danger mb-1"><i class="fas fa-exclamation-circle text-danger me-1"></i>0</div>
                                <small class="text-muted">Pelanggaran Detektif</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center p-3 bg-light rounded">
                                <div class="h5 text-info mb-1">
                                    @php
                                        $endTime = $session->waktu_selesai;
                                        $now = now();
                                        $remainingMinutes = max(0, $endTime->diffInMinutes($now));
                                    @endphp
                                    {{ $remainingMinutes }}m
                                </div>
                                <small class="text-muted">Sisa Waktu</small>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-gray-300 {
    color: #dddfeb !important;
}
.text-gray-800 {
    color: #5a5c69 !important;
}
</style>
@endpush
