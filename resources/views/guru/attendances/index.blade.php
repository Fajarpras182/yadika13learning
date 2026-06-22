@extends('layouts.app')

@section('title', 'Absensi Siswa - ' . $course->nama_mata_pelajaran . ' - E-Learning SMK Yadika 13')
@section('page-title', 'Absensi Siswa - ' . $course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item active" aria-current="page">Absensi</li>
    </ol>
</nav>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title">
                    <i class="fas fa-calendar-check"></i>Data Absensi Siswa
                </h6>
                <div>
                    <a href="{{ route('guru.attendances.create', $course->id) }}" class="btn btn-primary me-2">
                        <i class="fas fa-plus me-1"></i>Tambah Absensi
                    </a>
                    <a href="{{ route('guru.courses') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Kembali ke Mata Pelajaran
                    </a>
                </div>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <input type="date" name="tanggal_from" value="{{ request('tanggal_from') }}" class="form-control" placeholder="Dari">
                    </div>
                    <div class="col-md-3">
                        <input type="date" name="tanggal_to" value="{{ request('tanggal_to') }}" class="form-control" placeholder="Sampai">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach(['hadir','izin','sakit','alpa'] as $s)
                                <option value="{{ $s }}" {{ request('status')===$s?'selected':'' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-outline-primary"><i class="fas fa-search me-1"></i> Filter</button>
                    </div>
                </form>
                @if($attendances->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $index => $attendance)
                            <tr>
                                <td>{{ $loop->iteration + ($attendances->currentPage() - 1) * $attendances->perPage() }}</td>
                                <td>{{ $course->nama_mata_pelajaran }}</td>
                                <td>{{ $attendance->student->name }}</td>
                                <td>{{ $attendance->student->nis_nip }}</td>
                                <td>{{ $attendance->tanggal->format('d/m/Y') }}</td>
                                <td>
                                    @if($attendance->status == 'hadir')
                                        <span class="badge bg-success">Hadir</span>
                                    @elseif($attendance->status == 'izin')
                                        <span class="badge bg-warning">Izin</span>
                                    @elseif($attendance->status == 'sakit')
                                        <span class="badge bg-info">Sakit</span>
                                    @else
                                        <span class="badge bg-danger">Alpa</span>
                                    @endif
                                </td>
                                <td>{{ $attendance->keterangan ?: '-' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('guru.attendances.edit', [$course->id, $attendance->id]) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('guru.attendances.destroy', [$course->id, $attendance->id]) }}" class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus data absensi ini?')">
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

                @if($attendances->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $attendances->links() }}
                </div>
                @endif
                @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Belum ada data absensi</h5>
                    <p class="text-muted">Mulai dengan menambahkan data absensi siswa untuk mata pelajaran ini</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
