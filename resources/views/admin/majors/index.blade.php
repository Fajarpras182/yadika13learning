@extends('layouts.app')

@section('title', 'Jurusan - Admin')
@section('page-title', 'Manajemen Jurusan')

@section('content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <form class="d-flex" method="GET" action="{{ route('admin.majors.index') }}">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control me-2" placeholder="Cari kode/nama/deskripsi">
            <select name="status" class="form-select me-2">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Nonaktif</option>
            </select>
            <button class="btn btn-primary" type="submit"><i class="fas fa-filter me-1"></i> Filter</button>
        </form>
        <div class="d-flex gap-2">
            <form method="POST" action="{{ route('admin.majors.import') }}" enctype="multipart/form-data" class="d-flex">
                @csrf
                <input type="file" name="file" class="form-control form-control-sm me-2" accept=".xlsx,.csv" required>
                <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-file-import me-1"></i> Import</button>
            </form>
            <div class="btn-group">
                <a href="{{ route('admin.majors.export.excel') }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i></a>
                <a href="{{ route('admin.majors.export.csv') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-csv"></i></a>
                <a href="{{ route('admin.majors.export.pdf') }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a>
                <a href="{{ route('admin.majors.export.word') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-word"></i></a>
                <a href="{{ route('admin.majors.export.pdf') }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="fas fa-print"></i></a>
            </div>
            <a href="{{ route('admin.majors.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Tambah</a>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-modern">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Deskripsi</th>
                        <th>Status</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($majors as $major)
                    <tr>
                        <td><strong>{{ $major->code }}</strong></td>
                        <td>{{ $major->name }}</td>
                        <td class="text-muted small">{{ \Illuminate\Support\Str::limit($major->description, 80) }}</td>
                        <td>
                            <span class="badge bg-{{ $major->is_active ? 'success' : 'secondary' }}">{{ $major->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('admin.majors.edit', $major) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('admin.majors.destroy', $major) }}" onsubmit="return confirm('Hapus jurusan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Belum ada data</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $majors->links() }}
    </div>
</div>
@endsection


