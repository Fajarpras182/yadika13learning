@extends('layouts.app')

@section('title', 'Materi - '.$course->nama_mata_pelajaran)
@section('page-title', 'Materi - '.$course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item active" aria-current="page">Materi</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('guru.courses') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Mata Pelajaran
        </a>
    </div>
    <a href="{{ route('guru.lessons.create', $course->id) }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Tambah Materi
    </a>
  </div>

<div class="card shadow-md">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul/deskripsi materi">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="fas fa-search me-1"></i> Cari</button>
            </div>
        </form>

        @if($lessons->count() === 0)
            <div class="text-center text-muted py-5">
                <i class="fas fa-file-alt fa-2x mb-2"></i>
                <p class="mb-0">Belum ada materi. Klik tombol "Tambah Materi" untuk membuat.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 80px">Urutan</th>
                            <th>Judul</th>
                            <th>Deskripsi</th>
                            <th style="width: 160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lessons as $lesson)
                            <tr>
                                <td><span class="badge bg-primary">{{ $lesson->urutan }}</span></td>
                                <td>{{ $lesson->judul }}</td>
                                <td class="text-muted">{{ \Illuminate\Support\Str::limit($lesson->deskripsi, 100) }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-outline-primary" href="{{ route('guru.lessons.edit', [$course->id, $lesson->id]) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('guru.lessons.destroy', [$course->id, $lesson->id]) }}" onsubmit="return confirm('Hapus materi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $lessons->links() }}
        @endif
    </div>
</div>
@endsection


