@extends('layouts.app')

@section('title', 'Tugas Siswa')

@section('page-title', 'Tugas Siswa')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Semua Tugas Siswa</h3>
                    <div class="card-tools">
                        <a href="{{ route('guru.courses') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Buat Tugas Baru
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Judul Tugas</th>
                                        <th>Deadline</th>
                                        <th>Bobot Nilai</th>
                                        <th>Jumlah Siswa</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $index => $assignment)
                                        <tr>
                                            <td>{{ $assignments->firstItem() + $index }}</td>
                                            <td>{{ $assignment->course->nama }}</td>
                                            <td>{{ $assignment->judul }}</td>
                                            <td>{{ \Carbon\Carbon::parse($assignment->deadline)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $assignment->bobot_nilai }}%</td>
                                            <td>{{ $assignment->grades->count() }}</td>
                                            <td>
                                                @if($assignment->is_active)
                                                    <span class="badge bg-success">Aktif</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('guru.assignments.status', $assignment->id) }}" class="btn btn-info btn-sm" title="Lihat Status">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('guru.assignments.grade', $assignment->id) }}" class="btn btn-warning btn-sm" title="Beri Nilai">
                                                        <i class="fas fa-star"></i>
                                                    </a>
                                                    <a href="{{ route('guru.assignments.edit', [$assignment->course_id, $assignment->id]) }}" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('guru.assignments.destroy', [$assignment->course_id, $assignment->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
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
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                            <h4>Belum ada Tugas</h4>
                            <p class="text-muted">Mulai dengan membuat tugas pertama untuk siswa Anda.</p>
                            <a href="{{ route('guru.courses') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Buat Tugas Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
