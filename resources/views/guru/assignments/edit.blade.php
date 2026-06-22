@extends('layouts.app')

@section('title', 'Edit Tugas - '.$course->nama_mata_pelajaran)
@section('page-title', 'Edit Tugas - '.$course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.assignments', $course->id) }}">Tugas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
  </nav>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('guru.assignments.update', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $assignment->judul) }}" required>
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" required>{{ old('deskripsi', $assignment->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Upload File (Opsional)</label>
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept="*/*">
                <div class="form-text">Upload file tugas (semua jenis file didukung, maksimal 50MB)</div>
                @if($assignment->file_path)
                    <div class="mt-2">
                        <small class="text-muted">File saat ini: <a href="{{ Storage::url($assignment->file_path) }}" target="_blank">{{ basename($assignment->file_path) }}</a></small>
                    </div>
                @endif
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="datetime-local" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline', \Carbon\Carbon::parse($assignment->deadline)->format('Y-m-d\\TH:i')) }}" required>
                @error('deadline')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('guru.assignments', $course->id) }}" class="btn btn-outline-secondary">Kembali</a>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </form>

        <form method="POST" action="{{ route('guru.assignments.destroy', ['courseId' => $course->id, 'assignmentId' => $assignment->id]) }}" onsubmit="return confirm('Hapus tugas ini?')" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="fas fa-trash me-1"></i> Hapus Tugas
            </button>
        </form>
    </div>
</div>
@endsection


