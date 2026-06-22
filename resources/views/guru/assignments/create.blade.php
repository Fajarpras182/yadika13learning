@extends('layouts.app')

@section('title', 'Buat Tugas - '.$course->nama_mata_pelajaran)
@section('page-title', 'Buat Tugas - '.$course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.assignments', $course->id) }}">Tugas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Buat</li>
    </ol>
  </nav>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('guru.assignments.store', ['courseId' => $course->id]) }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Judul</label>
                <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}" required>
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Upload File (Opsional)</label>
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept="*/*">
                <div class="form-text">Upload file tugas (semua jenis file didukung, maksimal 50MB)</div>
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Deadline</label>
                <input type="datetime-local" name="deadline" class="form-control @error('deadline') is-invalid @enderror" value="{{ old('deadline') }}" required>
                @error('deadline')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('guru.assignments', $course->id) }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


