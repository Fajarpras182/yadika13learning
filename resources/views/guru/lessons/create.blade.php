@extends('layouts.app')

@section('title', 'Tambah Materi - '.$course->nama_mata_pelajaran)
@section('page-title', 'Tambah Materi - '.$course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.lessons', $course->id) }}">Materi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
    </ol>
  </nav>
<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('guru.lessons.store', ['courseId' => $course->id]) }}" enctype="multipart/form-data">
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
                <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Materi (Konten Pembelajaran)</label>
                <textarea name="materi" class="form-control @error('materi') is-invalid @enderror" rows="8" required>{{ old('materi') }}</textarea>
                <div class="form-text">Masukkan konten/materi pembelajaran untuk pelajaran ini</div>
                @error('materi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>



            <div class="mb-3">
                <label class="form-label">File Materi (Opsional)</label>
                <input type="file" name="file_materi" class="form-control @error('file_materi') is-invalid @enderror" accept="*/*">
                <div class="form-text">Upload file materi (semua jenis file didukung, maksimal 50MB)</div>
                @error('file_materi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">URL Video Pembelajaran (Opsional)</label>
                <input type="url" name="video_url" class="form-control @error('video_url') is-invalid @enderror" value="{{ old('video_url') }}" placeholder="https://www.youtube.com/watch?v=... atau https://vimeo.com/...">
                <div class="form-text">Masukkan URL video dari YouTube, Vimeo, atau platform video lainnya</div>
                @error('video_url')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Urutan</label>
                <input type="number" name="urutan" min="1" class="form-control @error('urutan') is-invalid @enderror" value="{{ old('urutan', 1) }}" required>
                @error('urutan')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('guru.lessons', $course->id) }}" class="btn btn-outline-secondary">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection


