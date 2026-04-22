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
                        <i class="fas fa-chart-bar me-2"></i>Laporan Nilai
                    </a>
                    <a href="{{ route('guru.profile') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-user me-2"></i>Profil
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


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
