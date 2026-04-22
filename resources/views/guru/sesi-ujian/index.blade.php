@extends('layouts.app')

@section('title', 'Manajemen Sesi Ujian - Guru')
@section('page-title', 'Daftar Ujian ({{ $sessions->count() }} )')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="fas fa-clock me-2"></i>
        Daftar Ujian ({{ $sessions->count() }})
    </h2>
    <div class="btn-group">
        <a href="{{ route('guru.sesi-ujian.create') }}" class="btn btn-primary" title="Buat Sesi Baru">
            <i class="fas fa-plus me-2"></i>Tambah Sesi Ujian
        </a>
        <button class="btn btn-outline-success" onclick="exportSessions()">
            <i class="fas fa-download me-2"></i>Export
        </button>
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
                        <th>Kelas Peserta</th>
                        <th>Sesi</th>
                        <th>Waktu Mulai</th>
                        <th>Waktu Selesai</th>


                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $session)
@php
                        $classIds = $session->ujian->class_ids ? (is_array($session->ujian->class_ids) ? $session->ujian->class_ids : json_decode($session->ujian->class_ids, true)) : [];
                        $kelasNames = [];
                        foreach($classIds as $id) {
                            $class = \App\Models\SchoolClass::find($id);
                            if($class) $kelasNames[] = $class->nama_kelas;
                        }
                        $kelasNames = implode(', ', $kelasNames) ?: '-';
                    @endphp
                    <tr>
                        <td>{{ ($sessions->currentPage() - 1) * $sessions->perPage() + $loop->iteration }}</td>
                        <td>{{ Str::limit($session->ujian->judul ?? '-', 30) }}</td>
                        <td>{{ $session->ujian->course->nama_mata_pelajaran ?? '-' }}</td>
                        <td><small class="text-muted">{{ $kelasNames }}</small></td>
                        <td>{{ Str::limit($session->nama_sesi ?? 'Sesi Default', 25) }}</td>
                        <td>{{ $session->waktu_mulai?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $session->waktu_selesai?->format('d/m/Y H:i') ?? '-' }}</td>


                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a href="{{ route('guru.sesi-ujian.show', $session->id) }}" class="btn btn-outline-info" title="Tambah Siswa / Detail">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="{{ route('guru.sesi-ujian.edit', $session->id) }}" class="btn btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('guru.sesi-ujian.destroy', $session->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus sesi {{ $session->nama_sesi ?? $session->id }}?')">
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
    {{ $sessions->appends(request()->query())->links() }}
</div>
@else
<div class="text-center py-5">
    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
    <h4 class="text-muted">Belum ada sesi ujian dibuat</h4>
    <p class="text-muted">Buat sesi ujian pertama Anda sekarang</p>
    <a href="{{ route('guru.sesi-ujian.create') }}" class="btn btn-primary btn-lg">
        <i class="fas fa-plus me-2"></i>Tambah Sesi Ujian
    </a>
</div>
@endif

@endsection

@push('scripts')
<script>
function exportSessions() {
    alert('Fitur export sesi ujian akan segera tersedia');
}

