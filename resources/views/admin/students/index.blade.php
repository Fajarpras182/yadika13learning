@extends('layouts.app')

@section('title', 'Manajemen Siswa - E-Learning SMK Yadika 13')
@section('page-title', 'Manajemen Siswa')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <form class="row g-2" method="GET" action="{{ route('admin.students.index') }}">
                    <div class="col-auto">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/email/NIS">
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
                    </div>
                </form>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.students.import') }}" enctype="multipart/form-data" class="d-flex">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm me-2" accept=".xlsx,.csv" required>
                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-file-import me-1"></i> Import</button>
                    </form>
                    <div class="btn-group">
                        <a href="{{ route('admin.students.export.excel') }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i></a>
                        <a href="{{ route('admin.students.export.csv') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-csv"></i></a>
                        <a href="{{ route('admin.students.export.pdf') }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a>
                        <a href="{{ route('admin.students.export.word') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-word"></i></a>
                        <a href="{{ route('admin.students.export.pdf') }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="fas fa-print"></i></a>
                    </div>
                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Siswa
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama</th>
                                <th>NIS</th>
                                <th>Jenis Kelamin</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                            <tr>
                                <td>{{ $loop->iteration + ($students->currentPage() - 1) * $students->perPage() }}</td>
                                <td>
                                    <strong>{{ $student->name }}</strong>
                                    @if($student->alamat)
                                        <br><small class="text-muted">{{ $student->alamat }}</small>
                                    @endif
                                </td>
                                <td>{{ $student->nis_nip }}</td>
                                <td>
                                    @if($student->jenis_kelamin == 'L')
                                        <span class="badge bg-info">Laki-laki</span>
                                    @elseif($student->jenis_kelamin == 'P')
                                        <span class="badge bg-warning">Perempuan</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>{{ $student->kelas ?: '-' }}</td>
                                <td>{{ $student->jurusan ?: '-' }}</td>
                                <td>
                                    @if($student->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$student->is_active)
                                            <form method="POST" action="{{ route('admin.students.activate', $student->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" title="Aktivasi">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.students.deactivate', $student->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-sm" title="Nonaktifkan">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.students.destroy', $student->id) }}" class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus siswa ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-user-graduate fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada siswa</h5>
                                    <p class="text-muted">Mulai dengan menambahkan siswa pertama</p>
                                    <a href="{{ route('admin.students.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Siswa
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($students->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $students->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
