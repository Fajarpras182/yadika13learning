@extends('layouts.app')

@section('title', 'Detail Tugas - Siswa')
@section('page-title', 'Detail Tugas')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('siswa.assignments') }}">Tugas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Tugas</li>
    </ol>
</nav>
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-3">
            <div class="card-header">
                <h6 class="m-0">{{ $assignment->judul ?? 'Tugas' }}</h6>
            </div>
            <div class="card-body">
                <p class="mb-2 text-muted">Mata Pelajaran: <strong>{{ $assignment->course->nama_mata_pelajaran ?? '-' }}</strong></p>
                <p class="mb-2">{{ $assignment->deskripsi ?? 'Tidak ada deskripsi.' }}</p>
                <p class="mb-0 text-muted">Batas Waktu: {{ optional($assignment->batas_waktu ?? $assignment->due_at)->format('d M Y H:i') }}</p>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0">Kumpulkan Jawaban</h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('siswa.assignments.submit', $assignment->id) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Jawaban Teks</label>
                        <textarea name="jawaban_text" rows="4" class="form-control">{{ old('jawaban_text', optional($grade)->jawaban_text) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File Jawaban (pdf, doc, docx, txt)</label>
                        <input type="file" name="file_jawaban" class="form-control">
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Kumpulkan</button>
                        <a href="{{ route('siswa.assignments') }}" class="btn btn-outline-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card shadow">
            <div class="card-header">
                <h6 class="m-0">Status</h6>
            </div>
            <div class="card-body">
                @if(isset($grade))
                    <p class="mb-2">Status: <span class="badge bg-success">Sudah dikumpulkan</span></p>
                    @if($grade->nilai)
                        <p class="mb-0">Nilai: <strong>{{ $grade->nilai }}</strong></p>
                    @endif
                @else
                    <p class="mb-0">Belum dikumpulkan.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection


