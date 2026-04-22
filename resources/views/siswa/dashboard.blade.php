@extends('layouts.app')

@section('title', 'Dashboard Siswa - E-Learning SMK Yadika 13')
@section('page-title', 'Dashboard Siswa')

@section('content')
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-item">
        <i class="fas fa-book stat-icon"></i>
        <span class="stat-value">{{ $enrolledCourses }}</span>
        <span class="stat-label">Mata Pelajaran</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-tasks stat-icon"></i>
        <span class="stat-value">{{ $pendingAssignments }}</span>
        <span class="stat-label">Tugas Pending</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-check-circle stat-icon"></i>
        <span class="stat-value">{{ $recentGrades->where('status', 'sudah_dinilai')->count() }}</span>
        <span class="stat-label">Tugas Selesai</span>
    </div>

    <div class="stat-item">
        <i class="fas fa-chart-line stat-icon"></i>
        <span class="stat-value">
            @php
                $gradedAssignments = $recentGrades->where('status', 'sudah_dinilai')->whereNotNull('nilai');
                $averageGrade = $gradedAssignments->count() > 0 ? $gradedAssignments->avg('nilai') : 0;
            @endphp
            {{ number_format($averageGrade, 1) }}
        </span>
        <span class="stat-label">Rata-rata Nilai</span>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('siswa.courses') }}" class="btn btn-primary">
                        <i class="fas fa-book me-2"></i>Lihat Mata Pelajaran
                    </a>
                    <a href="{{ route('siswa.assignments') }}" class="btn btn-outline-warning">
                        <i class="fas fa-tasks me-2"></i>Lihat Tugas
                    </a>
                    <a href="{{ route('siswa.grades') }}" class="btn btn-outline-success">
                        <i class="fas fa-chart-line me-2"></i>Lihat Nilai
                    </a>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Recent Grades -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-md">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Nilai Terbaru
                </h6>
            </div>
            <div class="card-body">
                @if($recentGrades->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Tugas</th>
                                <th>Mata Pelajaran</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentGrades as $grade)
                            <tr>
                                <td>
                                    <strong>{{ $grade->assignment->judul }}</strong>
                                </td>
                                <td>{{ $grade->assignment->course->nama_mata_pelajaran }}</td>
                                <td>
                                    @if($grade->nilai)
                                        <span class="badge bg-{{ $grade->nilai >= 80 ? 'success' : ($grade->nilai >= 70 ? 'warning' : 'danger') }}">
                                            {{ $grade->nilai }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @switch($grade->status)
                                        @case('belum_dikumpulkan')
                                            <span class="badge bg-danger">Belum Dikumpulkan</span>
                                            @break
                                        @case('sudah_dikumpulkan')
                                            <span class="badge bg-warning">Menunggu Penilaian</span>
                                            @break
                                        @case('sudah_dinilai')
                                            <span class="badge bg-success">Sudah Dinilai</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($grade->submitted_at)
                                        {{ $grade->submitted_at->format('d/m/Y') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-4">
                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada nilai</h5>
                    <p class="text-muted">Mulai dengan mengerjakan tugas dari mata pelajaran Anda</p>
                    <a href="{{ route('siswa.assignments') }}" class="btn btn-primary">
                        <i class="fas fa-tasks me-2"></i>Lihat Tugas
                    </a>
                </div>
                @endif
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
