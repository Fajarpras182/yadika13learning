@extends('layouts.app')

@section('title', 'Tugas - '.$course->nama_mata_pelajaran)
@section('page-title', 'Tugas - '.$course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tugas</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="{{ route('guru.courses') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Mata Pelajaran
        </a>
    </div>
    <a href="{{ route('guru.assignments.create', $course->id) }}" class="btn btn-primary">
        <i class="fas fa-plus me-1"></i> Buat Tugas
    </a>
  </div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul/deskripsi/instruksi">
            </div>
            <div class="col-auto">
                <button class="btn btn-outline-primary"><i class="fas fa-search me-1"></i> Cari</button>
            </div>
        </form>

        @if($assignments->count() === 0)
            <div class="text-center text-muted py-5">
                <i class="fas fa-tasks fa-2x mb-2"></i>
                <p class="mb-0">Belum ada tugas untuk mata pelajaran ini.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Deadline</th>
                            <th>Bobot</th>
                            <th>Telah Dinilai</th>
                            <th width="200">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                            <tr>
                                <td>{{ $assignment->judul }}</td>
                                <td>{{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y H:i') }}</td>
                                <td><span class="badge bg-primary">{{ $assignment->bobot_nilai }}</span></td>
                                <td>{{ $assignment->grades->where('status','sudah_dinilai')->count() }} / {{ $assignment->grades->count() }}</td>
                                <td>
                                    <a href="{{ route('guru.assignments.status', $assignment->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye me-1"></i> Lihat Status
                                    </a>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a class="btn btn-outline-secondary" href="{{ route('guru.assignments.edit', [$course->id, $assignment->id]) }}">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a class="btn btn-outline-primary" href="{{ route('guru.assignments.grade', $assignment->id) }}">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                        <form method="POST" action="{{ route('guru.assignments.destroy', [$course->id, $assignment->id]) }}" class="d-inline" onsubmit="return confirm('Hapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $assignments->links() }}
        @endif
    </div>
</div>
@endsection


