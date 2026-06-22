@extends('layouts.app')

@section('title', $lesson->judul . ' - E-Learning SMK Yadika 13')
@section('page-title', $lesson->judul)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('siswa.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item"><a href="{{ route('siswa.courses.show', $course->id) }}">{{ $course->nama_mata_pelajaran }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $lesson->judul }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <!-- Lesson Content -->
        <div class="card shadow-md mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-book me-2"></i>Materi Pembelajaran
                </h6>
            </div>
            <div class="card-body">
                @if($lesson->deskripsi)
                <div class="lesson-description mb-4">
                    <h5>Deskripsi</h5>
                    <p>{{ $lesson->deskripsi }}</p>
                </div>
                @endif

                @if($lesson->video_url)
                <div class="lesson-video mb-4">
                    <h5>Video Pembelajaran</h5>
                    <div class="ratio ratio-16x9">
                        <iframe src="{{ $lesson->video_url }}" title="Video Pembelajaran" allowfullscreen></iframe>
                    </div>
                </div>
                @endif

                @if($lesson->file_materi)
                <div class="lesson-file mb-4">
                    <h5>File Materi</h5>
                    <div class="d-flex align-items-center p-3 border rounded">
                        <i class="fas fa-file fa-2x text-primary me-3"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">{{ basename($lesson->file_materi) }}</h6>
                            <small class="text-muted">File materi pembelajaran</small>
                        </div>
                        <a href="{{ route('siswa.download', ['type' => 'lessons', 'filename' => basename($lesson->file_materi)]) }}" class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
                @endif

                @if(!$lesson->deskripsi && !$lesson->video_url && !$lesson->file_materi)
                <div class="text-center py-4">
                    <i class="fas fa-book fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Materi pembelajaran belum lengkap</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Lesson Info -->
        <div class="card shadow-md mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-info-circle me-2"></i>Informasi
                </h6>
            </div>
            <div class="card-body">
                <p><strong>Mata Pelajaran:</strong> {{ $course->nama_mata_pelajaran }}</p>
                <p><strong>Guru:</strong> {{ $course->guru->name }}</p>
                <p><strong>Kelas:</strong> {{ $course->schoolClass->name ?? 'N/A' }}</p>
                @if($lesson->urutan)
                <p><strong>Urutan:</strong> {{ $lesson->urutan }}</p>
                @endif
            </div>
        </div>

        <!-- Navigation -->
        <div class="card shadow-md">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-arrow-left me-2"></i>Navigasi
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('siswa.courses.show', $course->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali ke Mata Pelajaran
                    </a>
                    <a href="{{ route('siswa.assignments') }}" class="btn btn-outline-success">
                        <i class="fas fa-tasks me-2"></i>Lihat Tugas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
