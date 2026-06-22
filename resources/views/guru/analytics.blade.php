@extends('layouts.app')

@section('title', 'Analytics Dashboard - Guru')
@section('page-title', 'Analytics & Reporting Dashboard')

@section('content')
<!-- Header with Quick Stats -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2><i class="fas fa-chart-line me-2 text-primary"></i>Analytics & Reporting Dashboard</h2>
            <a href="{{ route('guru.reports') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Laporan
            </a>
        </div>
    </div>
</div>

<!-- Score Statistics -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="h6 text-muted">Total Ujian Dibuat</div>
                <div class="h3 text-primary font-weight-bold">{{ $totalExams }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="h6 text-muted">Ujian yang Dikerjakan</div>
                <div class="h3 text-success font-weight-bold">{{ $totalExamsTaken }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="h6 text-muted">Rata-rata Skor</div>
                <div class="h3 text-info font-weight-bold">{{ $scoreStats['average'] ?? 0 }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="h6 text-muted">Skor Tertinggi</div>
                <div class="h3 text-warning font-weight-bold">{{ $scoreStats['highest'] ?? 0 }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Analysis -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Analisis Performa Siswa</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Nilai ≥ 70 (Lulus)</span>
                            <span class="badge bg-success">{{ $scoreStats['above_70'] ?? 0 }}</span>
                        </div>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $totalExamsTaken > 0 ? ($scoreStats['above_70'] / $totalExamsTaken * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Nilai ≥ 80 (Bagus)</span>
                            <span class="badge bg-info">{{ $scoreStats['above_80'] ?? 0 }}</span>
                        </div>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $totalExamsTaken > 0 ? ($scoreStats['above_80'] / $totalExamsTaken * 100) : 0 }}%"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Nilai ≥ 90 (Sangat Bagus)</span>
                            <span class="badge bg-warning">{{ $scoreStats['above_90'] ?? 0 }}</span>
                        </div>
                        <div class="progress mt-2">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalExamsTaken > 0 ? ($scoreStats['above_90'] / $totalExamsTaken * 100) : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Statistics -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Statistik Tugas</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Total Tugas</span>
                        <span class="badge bg-primary">{{ $assignmentStats['total'] ?? 0 }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Sudah Dinilai</span>
                        <span class="badge bg-success">{{ $assignmentStats['graded'] ?? 0 }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $assignmentStats['total'] > 0 ? ($assignmentStats['graded'] / $assignmentStats['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Belum Dinilai</span>
                        <span class="badge bg-warning">{{ $assignmentStats['pending'] ?? 0 }}</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $assignmentStats['total'] > 0 ? ($assignmentStats['pending'] / $assignmentStats['total'] * 100) : 0 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Course-wise Performance -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-book me-2"></i>Performa Per Mata Pelajaran</h6>
            </div>
            <div class="card-body p-0">
                @if($courseStats->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Ujian</th>
                                <th width="100">Ujian Diambil</th>
                                <th width="120">Rata-rata</th>
                                <th width="100">Pass Rate</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courseStats as $stat)
                            <tr>
                                <td>
                                    <strong>{{ $stat['course_name'] }}</strong>
                                </td>
                                <td>{{ $stat['exam_title'] }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $stat['total_taken'] }}</span>
                                </td>
                                <td>
                                    <div class="progress progress-sm">
                                        <div class="progress-bar" role="progressbar" style="width: {{ $stat['average_score'] }}%"></div>
                                    </div>
                                    <small class="text-muted">{{ $stat['average_score'] }}/100</small>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $stat['pass_rate'] >= 70 ? 'success' : 'warning' }}">
                                        {{ $stat['pass_rate'] }}%
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada data ujian</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Exam Results -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h6 class="mb-0"><i class="fas fa-history me-2"></i>10 Hasil Ujian Terbaru</h6>
            </div>
            <div class="card-body p-0">
                @if($recentResults->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Mata Pelajaran</th>
                                <th>Ujian</th>
                                <th width="100">Skor</th>
                                <th width="150">Waktu Selesai</th>
                                <th width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentResults as $result)
                            <tr>
                                <td>
                                    <strong>{{ $result->student->name }}</strong>
                                    @if($result->student->nis_nip)
                                    <br><small class="text-muted">{{ $result->student->nis_nip }}</small>
                                    @endif
                                </td>
                                <td>{{ $result->sesiUjian->ujian->course->nama_mata_pelajaran ?? '-' }}</td>
                                <td>{{ $result->sesiUjian->ujian->judul ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-{{ $result->score >= 70 ? 'success' : 'danger' }}">
                                        {{ $result->score ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $result->created_at->format('d M Y H:i') ?? '-' }}
                                    </small>
                                </td>
                                <td>
                                    <a href="{{ route('guru.nilai-ujian.review', $result->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada hasil ujian</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
