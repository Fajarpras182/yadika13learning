@extends('layouts.app')

@section('title', 'Edit Pesan')
@section('page-title', 'Edit Pesan')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.messages') }}">Forum Diskusi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Pesan</li>
    </ol>
</nav>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('guru.messages.update', $message->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label class="form-label">Subjek</label>
                <input type="text" name="subject" class="form-control @error('subject') is-invalid @enderror" value="{{ old('subject', $message->subject) }}" required>
                @error('subject')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Pesan</label>
                <textarea name="message" class="form-control @error('message') is-invalid @enderror" rows="6" required>{{ old('message', $message->message) }}</textarea>
                @error('message')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">File Lampiran (Opsional)</label>
                <input type="file" name="file" class="form-control @error('file') is-invalid @enderror" accept="*/*">
                <div class="form-text">Upload file lampiran baru (semua jenis file didukung, maksimal 50MB)</div>
                @if($message->file_path)
                    <div class="mt-2">
                        <small class="text-muted">File saat ini: <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank">{{ basename($message->file_path) }}</a></small>
                    </div>
                @endif
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('guru.messages') }}" class="btn btn-outline-secondary">Kembali</a>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
