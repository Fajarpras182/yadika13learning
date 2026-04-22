@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('page-title', 'Mata Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Mata Pelajaran</h3>
                    <div class="card-tools">
                        <a href="{{ route('guru.courses.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Mata Pelajaran
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($courses->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode</th>
                                        <th>Nama Mata Pelajaran</th>
                                        <th>Deskripsi</th>
                                        <th>Jumlah Siswa</th>
                                        <th>Jumlah Materi</th>
                                        <th>Jumlah Tugas</th>
                                        <th>Hari-Waktu</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courses as $index => $course)
                                        <tr>
                                            <td>{{ $courses->firstItem() + $index }}</td>
                                            <td>{{ $course->kode_mata_pelajaran }}</td>
                                            <td>{{ $course->nama_mata_pelajaran }}</td>
                                            <td>{{ Str::limit($course->deskripsi, 50) }}</td>
                                            <td>{{ $course->schoolClass->students->count() ?? 0 }}</td>
                                            <td>{{ $course->lessons->count() }}</td>
                                            <td>{{ $course->assignments->count() }}</td>
                                            <td>
                                                @if($course->schedules->count() > 0)
                                                    @foreach($course->schedules as $schedule)
                                                        <div class="mb-1">
                                                            <small class="text-muted">
                                                                {{ $schedule->day }}: {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                                                @if($schedule->schoolClass)
                                                                    ({{ $schedule->schoolClass->name }})
                                                                @endif
                                                            </small>
                                                        </div>
                                                    @endforeach
                                                @else
                                                    <small class="text-muted">Belum ada jadwal</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="{{ route('guru.lessons', $course->id) }}" class="btn btn-info btn-sm" title="Materi">
                                                        <i class="fas fa-book"></i>
                                                    </a>
                                                    <a href="{{ route('guru.assignments', $course->id) }}" class="btn btn-warning btn-sm" title="Tugas">
                                                        <i class="fas fa-tasks"></i>
                                                    </a>
                                                    <a href="{{ route('guru.attendances', $course->id) }}" class="btn btn-success btn-sm" title="Presensi">
                                                        <i class="fas fa-calendar-check"></i>
                                                    </a>
                                                    <a href="{{ route('guru.courses.edit', $course->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('guru.courses.destroy', $course->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
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
                        {{ $courses->links() }}
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-3x text-muted mb-3"></i>
                            <h4>Belum ada Mata Pelajaran</h4>
                            <p class="text-muted">Mulai dengan membuat mata pelajaran pertama Anda.</p>
                            <a href="{{ route('guru.courses.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Mata Pelajaran
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
