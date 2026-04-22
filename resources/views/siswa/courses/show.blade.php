@extends('layouts.app')

@section('title', $course->nama_mata_pelajaran . ' - E-Learning SMK Yadika 13')
@section('page-title', $course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('siswa.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $course->nama_mata_pelajaran }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <!-- Course Info -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Informasi Mata Pelajaran
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Kode:</strong> {{ $course->kode_mata_pelajaran }}</p>
                        <p><strong>Guru:</strong> {{ $course->guru->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Kelas:</strong> {{ $course->schoolClass->name ?? 'N/A' }}</p>
                        <p><strong>Jurusan:</strong> {{ $course->schoolClass->major->name ?? 'N/A' }}</p>
                    </div>
                </div>
                @if($course->deskripsi)
                <div class="mt-3">
                    <strong>Deskripsi:</strong>
                    <p>{{ $course->deskripsi }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Lessons -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-book me-2"></i>Materi Pembelajaran
                </h6>
            </div>
            <div class="card-body">
                @forelse($course->lessons->sortBy('urutan') as $lesson)
                <div class="lesson-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $lesson->judul }}</h6>
                            @if($lesson->deskripsi)
                            <p class="text-muted small mb-2">{{ Str::limit($lesson->deskripsi, 100) }}</p>
                            @endif
                            <div class="d-flex gap-2">
                                @if($lesson->file_materi)
                                <small class="text-info">
                                    <i class="fas fa-file me-1"></i>File tersedia
                                </small>
                                @endif
                                @if($lesson->video_url)
                                <small class="text-success">
                                    <i class="fas fa-video me-1"></i>Video tersedia
                                </small>
                                @endif
                            </div>
                        </div>
                        <a href="{{ route('siswa.lessons.show', [$course->id, $lesson->id]) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada materi pembelajaran</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Assignments -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks me-2"></i>Tugas
                </h6>
            </div>
            <div class="card-body">
                @forelse($course->assignments as $assignment)
                <div class="assignment-item mb-3 p-3 border rounded">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ $assignment->judul }}</h6>
                            <p class="text-muted small mb-2">{{ Str::limit($assignment->deskripsi, 100) }}</p>
                            <div class="d-flex gap-2 mb-2">
                                <small class="text-warning">
                                    <i class="fas fa-clock me-1"></i>Deadline: {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y H:i') }}
                                </small>
                                @if($assignment->file_path)
                                <small class="text-info">
                                    <i class="fas fa-file me-1"></i>File tersedia
                                </small>
                                @endif
                            </div>
                            @php
                                $studentGrade = $grades->where('assignment_id', $assignment->id)->first();
                            @endphp
                            @if($studentGrade && $studentGrade->nilai)
                            <span class="badge bg-success">Dinilai: {{ $studentGrade->nilai }}</span>
                            @elseif($studentGrade && $studentGrade->status == 'sudah_dikumpulkan')
                            <span class="badge bg-info">Sudah Dikerjakan</span>
                            @else
                            <span class="badge bg-secondary">Belum Dikerjakan</span>
                            @endif
                        </div>
                        <a href="{{ route('siswa.assignments.show', $assignment->id) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-eye me-1"></i>Lihat
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada tugas</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Grades Summary -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Ringkasan Nilai
                </h6>
            </div>
            <div class="card-body">
                @php
                    $totalGrades = $grades->whereNotNull('nilai')->count();
                    $averageGrade = $totalGrades > 0 ? $grades->whereNotNull('nilai')->avg('nilai') : 0;
                @endphp
                <div class="text-center mb-3">
                    <div class="h4 text-primary mb-0">{{ number_format($averageGrade, 1) }}</div>
                    <small class="text-muted">Rata-rata Nilai</small>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h6 text-success mb-0">{{ $totalGrades }}</div>
                            <small class="text-muted">Dinilai</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="border rounded p-2">
                            <div class="h6 text-info mb-0">{{ $course->assignments->count() }}</div>
                            <small class="text-muted">Total Tugas</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-bolt me-2"></i>Aksi Cepat
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('siswa.assignments') }}" class="btn btn-outline-primary">
                        <i class="fas fa-tasks me-2"></i>Lihat Semua Tugas
                    </a>
                    <a href="{{ route('siswa.grades') }}" class="btn btn-outline-success">
                        <i class="fas fa-chart-line me-2"></i>Lihat Semua Nilai
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
