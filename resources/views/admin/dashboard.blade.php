@extends('layouts.app')

@section('title', 'Dashboard Admin - E-Learning SMK Yadika 13')
@section('page-title', 'Dashboard Admin')

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-item">
        <i class="fas fa-users stat-icon"></i>
        <span class="stat-value">{{ $totalUsers }}</span>
        <span class="stat-label">Total Users</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-chalkboard-teacher stat-icon"></i>
        <span class="stat-value">{{ $totalGuru }}</span>
        <span class="stat-label">Total Guru</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-user-graduate stat-icon"></i>
        <span class="stat-value">{{ $totalSiswa }}</span>
        <span class="stat-label">Total Siswa</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-book stat-icon"></i>
        <span class="stat-value">{{ $totalCourses }}</span>
        <span class="stat-label">Mata Pelajaran</span>
    </div>
</div>

<div class="row">
    <!-- Pending Users Alert -->
    @if($pendingUsers > 0)
    <div class="col-12 mb-4">
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Perhatian!</strong> Ada {{ $pendingUsers }} user yang menunggu aktivasi.
            <a href="{{ route('admin.teachers.index') }}" class="alert-link">Kelola guru</a> atau
            <a href="{{ route('admin.students.index') }}" class="alert-link">kelola siswa di sini</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
    @endif

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
                    <a href="{{ route('admin.data-master') }}" class="btn btn-primary">
                        <i class="fas fa-database me-2"></i>Data Master
                    </a>
                    <a href="{{ route('admin.teachers.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chalkboard-teacher me-2"></i>Kelola Guru
                    </a>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-user-graduate me-2"></i>Kelola Siswa
                    </a>
                    <a href="{{ route('admin.profile') }}" class="btn btn-outline-success">
                        <i class="fas fa-user-edit me-2"></i>Set Profil Admin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="col-lg-6 mb-4">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <i class="fas fa-info-circle dashboard-card-icon"></i>
                <h5>Informasi Sistem</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-primary">{{ $totalGuru }}</div>
                            <div class="text-muted small">Guru Aktif</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-success">{{ $totalSiswa }}</div>
                            <div class="text-muted small">Siswa Aktif</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-info">{{ $totalCourses }}</div>
                            <div class="text-muted small">Mata Pelajaran</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="text-center">
                            <div class="h4 text-warning">{{ $pendingUsers }}</div>
                            <div class="text-muted small">Pending Users</div>
                        </div>
                    </div>
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
