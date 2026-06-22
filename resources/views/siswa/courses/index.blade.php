@extends('layouts.app')

@section('title', 'Mata Pelajaran - E-Learning SMK Yadika 13')
@section('page-title', 'Mata Pelajaran')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Mata Pelajaran</li>
    </ol>
</nav>
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-book me-2"></i>Mata Pelajaran Saya
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($courses as $course)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-md">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h5 class="card-title text-primary">{{ $course->nama_mata_pelajaran }}</h5>
                                    <span class="badge bg-info">{{ $course->kode_mata_pelajaran }}</span>
                                </div>
                                
                                <p class="card-text text-muted small mb-3">
                                    {{ \Illuminate\Support\Str::limit($course->deskripsi, 100) }}
                                </p>
                                
                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-chalkboard-teacher me-1"></i>{{ $course->guru->name ?? 'N/A' }}
                                        <br>
                                        <i class="fas fa-graduation-cap me-1"></i>{{ $course->major->name ?? 'N/A' }}
                                        <br>
                                        <i class="fas fa-calendar me-1"></i>Semester {{ $course->semester }}
                                    </small>
                                </div>
                                
                                <div class="row text-center mb-3">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <div class="h6 text-primary mb-0">{{ $course->lessons->count() }}</div>
                                            <small class="text-muted">Materi</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <div class="h6 text-warning mb-0">{{ $course->assignments->count() }}</div>
                                            <small class="text-muted">Tugas</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-grid">
                                    <a href="{{ route('siswa.courses.show', $course->id) }}" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum terdaftar di mata pelajaran</h5>
                            <p class="text-muted">Hubungi admin untuk mendaftarkan Anda ke mata pelajaran</p>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                @if($courses->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $courses->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
