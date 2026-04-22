@extends('layouts.app')

@section('title', 'Manajemen Sesi Ujian - Admin')
@section('page-title', 'Daftar Sesi Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="fas fa-clock me-2"></i>
        Daftar Sesi Ujian
    </h2>
    <div>
        <a href="{{ route('admin.sesi-ujian.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tambah Sesi Ujian
        </a>
    </div>
</div>

@if($sessions->count() > 0)
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Ujian</th>
                        <th>Mata Pelajaran</th>
                        <th>Sesi</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>
                        <th>Status</th>
                        <th>Siswa</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
                    <tr>
                        <td>{{ ($sessions->currentPage() - 1) * $sessions->perPage() + $loop->iteration }}</td>
                        <td>{{ Str::limit($session->ujian->judul ?? '-', 30) }}</td>
                        <td>{{ $session->ujian->course->nama_mata_pelajaran ?? '-' }}</td>
                        <td>{{ Str::limit($session->nama_sesi ?? '-', 25) }}</td>
                        <td>{{ $session->waktu_mulai?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-' }} WIB</td>
                        <td>{{ $session->waktu_selesai?->timezone('Asia/Jakarta')->format('d/m/Y H:i') ?? '-' }} WIB</td>
                        <td>
                            @if($session->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-secondary">Tidak Aktif</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info">{{ $session->students->count() }}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('admin.sesi-ujian.show', $session->id) }}" class="btn btn-outline-info" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.sesi-ujian.edit', $session->id) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.sesi-ujian.destroy', $session->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus sesi \"' + '{{ addslashes($session->nama_sesi ?? $session->id) }}' + '\"?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
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
    </div>
</div>

<div class="mt-3">
    {{ $sessions->links() }}
</div>
@else
<div class="text-center py-5">
    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
    <h4 class="text-muted">Belum ada sesi ujian</h4>
    <p class="text-muted">Buat sesi ujian baru untuk mengelola ujian siswa.</p>
    <a href="{{ route('admin.sesi-ujian.create') }}" class="btn btn-primary btn-lg">
        <i class="fas fa-plus me-2"></i>Tambah Sesi Ujian
    </a>
</div>
@endif
@endsection
