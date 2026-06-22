@extends('layouts.app')

@section('title', 'Status Tugas - '.$assignment->judul)
@section('page-title', 'Status Tugas - '.$assignment->judul)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.assignments', $assignment->course_id) }}">Tugas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Status</li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('guru.assignments', $assignment->course_id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tugas
            </a>
        </div>

        <div class="mb-4">
            <h5 class="mb-1">{{ $assignment->judul }}</h5>
            <div class="text-muted">{{ $assignment->deskripsi }}</div>
            <div class="mt-2">
                <small class="text-muted">
                    <strong>Deadline:</strong> {{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y H:i') }} |
                    <strong>Bobot Nilai:</strong> {{ $assignment->bobot_nilai }}
                </small>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Status Pengumpulan</th>
                        <th>Waktu Pengumpulan</th>
                        <th>Status Penilaian</th>
                        <th>Nilai</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignment->grades as $grade)
                        <tr>
                            <td>{{ $grade->student->name }}</td>
                            <td>
                                @if($grade->submitted_at)
                                    <span class="badge bg-success">Sudah Mengumpulkan</span>
                                @else
                                    <span class="badge bg-warning">Belum Mengumpulkan</span>
                                @endif
                            </td>
                            <td>
                                @if($grade->submitted_at)
                                    {{ \Carbon\Carbon::parse($grade->submitted_at)->format('d M Y H:i') }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($grade->status === 'sudah_dinilai')
                                    <span class="badge bg-success">Sudah Dinilai</span>
                                @elseif($grade->submitted_at)
                                    <span class="badge bg-info">Menunggu Penilaian</span>
                                @else
                                    <span class="badge bg-secondary">Belum Dikerjakan</span>
                                @endif
                            </td>
                            <td>
                                @if($grade->nilai)
                                    <span class="badge bg-primary">{{ $grade->nilai }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data siswa untuk tugas ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success">{{ $assignment->grades->where('submitted_at', '!=', null)->count() }}</h5>
                            <p class="card-text">Sudah Mengumpulkan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-warning">{{ $assignment->grades->where('submitted_at', null)->count() }}</h5>
                            <p class="card-text">Belum Mengumpulkan</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-info">{{ $assignment->grades->where('submitted_at', '!=', null)->where('status', '!=', 'sudah_dinilai')->count() }}</h5>
                            <p class="card-text">Menunggu Penilaian</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title text-success">{{ $assignment->grades->where('status', 'sudah_dinilai')->count() }}</h5>
                            <p class="card-text">Sudah Dinilai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
