@extends('layouts.app')

@section('title', 'Manajemen Guru - E-Learning SMK Yadika 13')
@section('page-title', 'Manajemen Guru')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <form class="row g-2" method="GET" action="{{ route('admin.teachers.index') }}">
                    <div class="col-auto">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/email/NIP">
                    </div>
                    <div class="col-auto">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-filter me-1"></i> Filter</button>
                    </div>
                </form>
                <div class="d-flex gap-2">
                    <form method="POST" action="{{ route('admin.teachers.import') }}" enctype="multipart/form-data" class="d-flex">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm me-2" accept=".xlsx,.csv" required>
                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-file-import me-1"></i> Import</button>
                    </form>
                    <div class="btn-group">
                        <a href="{{ route('admin.teachers.export.excel') }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i></a>
                        <a href="{{ route('admin.teachers.export.csv') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-csv"></i></a>
                        <a href="{{ route('admin.teachers.export.pdf') }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a>
                        <a href="{{ route('admin.teachers.export.word') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-word"></i></a>
                        <a href="{{ route('admin.teachers.export.pdf') }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="fas fa-print"></i></a>
                    </div>
                    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Guru
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>NIP</th>
                                <th>No. HP</th>
                                <th>Jenis Kelamin</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($teachers as $teacher)
                            <tr>
                                <td>
                                    <strong>{{ $teacher->name }}</strong>
                                    @if($teacher->alamat)
                                        <br><small class="text-muted">{{ $teacher->alamat }}</small>
                                    @endif
                                </td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ $teacher->nis_nip }}</td>
                                <td>{{ $teacher->no_hp ?: '-' }}</td>
                                <td>
                                    @if($teacher->jenis_kelamin == 'L')
                                        <span class="badge bg-info">Laki-laki</span>
                                    @elseif($teacher->jenis_kelamin == 'P')
                                        <span class="badge bg-warning">Perempuan</span>
                                    @else
                                        <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($teacher->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.teachers.edit', $teacher->id) }}" class="btn btn-primary btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$teacher->is_active)
                                            <form method="POST" action="{{ route('admin.teachers.activate', $teacher->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" title="Aktivasi">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.teachers.deactivate', $teacher->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-sm" title="Nonaktifkan">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.teachers.destroy', $teacher->id) }}" class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus guru ini?')">
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
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-chalkboard-teacher fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada guru</h5>
                                    <p class="text-muted">Mulai dengan menambahkan guru pertama</p>
                                    <a href="{{ route('admin.teachers.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah Guru
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($teachers->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $teachers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
