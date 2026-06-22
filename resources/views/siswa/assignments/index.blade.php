@extends('layouts.app')

@section('title', 'Tugas - E-Learning SMK Yadika 13')
@section('page-title', 'Tugas Saya')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tugas</li>
    </ol>
</nav>

<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-tasks me-2"></i>Daftar Tugas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    @forelse($assignments as $assignment)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="card-title text-primary">{{ $assignment->judul }}</h6>
                                    @php
                                        $grade = $assignment->grades->first();
                                    @endphp
                                    @if($grade && $grade->nilai)
                                    <span class="badge bg-success">{{ $grade->nilai }}</span>
                                    @elseif($grade && $grade->status == 'sudah_dikumpulkan')
                                    <span class="badge bg-info">Dikerjakan</span>
                                    @else
                                    <span class="badge bg-secondary">Belum</span>
                                    @endif
                                </div>

                                <p class="card-text text-muted small mb-3">
                                    {{ Str::limit($assignment->deskripsi, 80) }}
                                </p>

                                <div class="mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-book me-1"></i>{{ $assignment->course->nama_mata_pelajaran }}
                                        <br>
                                        <i class="fas fa-chalkboard-teacher me-1"></i>{{ $assignment->course->guru->name }}
                                        <br>
                                        <i class="fas fa-clock me-1"></i>Deadline: {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y H:i') }}
                                    </small>
                                </div>

                                <div class="d-grid">
                                    <a href="{{ route('siswa.assignments.show', $assignment->id) }}" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada tugas</h5>
                            <p class="text-muted">Tugas akan muncul di sini ketika guru mengupload</p>
                        </div>
                    </div>
                    @endforelse
                </div>

                @if($assignments->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $assignments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
