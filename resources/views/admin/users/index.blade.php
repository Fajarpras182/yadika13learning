@extends('layouts.app')

@section('title', 'Manajemen User - E-Learning SMK Yadika 13')
@section('page-title', 'Manajemen User')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <form class="row g-2" method="GET" action="{{ route('admin.users') }}">
                    <div class="col-auto">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama/email/NIS-NIP">
                    </div>
                    <div class="col-auto">
                        <select name="role" class="form-select">
                            <option value="">Semua Role</option>
                            <option value="admin" {{ request('role')=='admin' ? 'selected' : '' }}>Admin</option>
                            <option value="guru" {{ request('role')=='guru' ? 'selected' : '' }}>Guru</option>
                            <option value="siswa" {{ request('role')=='siswa' ? 'selected' : '' }}>Siswa</option>
                        </select>
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
                    <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data" class="d-flex">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm me-2" accept=".xlsx,.csv" required>
                        <button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-file-import me-1"></i> Import</button>
                    </form>
                    <div class="btn-group">
                        <a href="{{ route('admin.users.export.excel') }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i></a>
                        <a href="{{ route('admin.users.export.csv') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-csv"></i></a>
                        <a href="{{ route('admin.users.export.pdf') }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a>
                        <a href="{{ route('admin.users.export.word') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-word"></i></a>
                        <a href="{{ route('admin.users.export.pdf') }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="fas fa-print"></i></a>
                    </div>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Tambah User
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
                                <th>Role</th>
                                <th>NIS/NIP</th>
                                <th>Kelas/Jurusan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->name }}</strong>
                                    @if($user->no_hp)
                                        <br><small class="text-muted">{{ $user->no_hp }}</small>
                                    @endif
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $user->role == 'admin' ? 'danger' : ($user->role == 'guru' ? 'success' : 'primary') }}">
                                        {{ ucfirst($user->role) }}
                                    </span>
                                </td>
                                <td>{{ $user->nis_nip }}</td>
                                <td>
                                    @if($user->kelas && $user->jurusan)
                                        {{ $user->kelas }} - {{ $user->jurusan }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        @if(!$user->is_active)
                                            <form method="POST" action="{{ route('admin.users.activate', $user->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" title="Aktivasi">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.users.deactivate', $user->id) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-warning btn-sm" title="Nonaktifkan">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Belum ada user</h5>
                                    <p class="text-muted">Mulai dengan menambahkan user pertama</p>
                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Tambah User
                                    </a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($users->hasPages())
                <div class="d-flex justify-content-center">
                    {{ $users->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
