@extends('layouts.app')

@section('title', 'Detail Sesi Ujian - Admin')
@section('page-title', 'Detail Sesi Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2><i class="fas fa-info-circle me-2"></i>Detail Sesi Ujian</h2>
        <small class="text-muted">{{ $sesi->nama_sesi }}</small>
    </div>
    <div class="btn-group">
        <a href="{{ route('admin.sesi-ujian.edit', $sesi->id) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Edit
        </a>
        <a href="{{ route('admin.sesi-ujian') }}" class="btn btn-secondary">
            <i class="fas fa-list me-2"></i>Daftar Sesi
        </a>
    </div>
</div>

{{-- Detail Card --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-5">Nama Sesi</dt>
                    <dd class="col-sm-7">{{ $sesi->nama_sesi }}</dd>

                    <dt class="col-sm-5">Ujian</dt>
                    <dd class="col-sm-7">{{ $sesi->ujian->judul ?? '-' }}</dd>

                    <dt class="col-sm-5">Mata Pelajaran</dt>
                    <dd class="col-sm-7">{{ $sesi->ujian->course->nama_mata_pelajaran ?? '-' }}</dd>

                    <dt class="col-sm-5">Waktu Mulai</dt>
                    <dd class="col-sm-7">{{ $sesi->waktu_mulai?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }} WIB</dd>

                    <dt class="col-sm-5">Waktu Selesai</dt>
                    <dd class="col-sm-7">{{ $sesi->waktu_selesai?->timezone('Asia/Jakarta')->format('d M Y H:i') ?? '-' }} WIB</dd>
                </dl>
            </div>
            <div class="col-md-6">
                <dl class="row">
                    <dt class="col-sm-5">Status</dt>
                    <dd class="col-sm-7">
                        @if($sesi->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </dd>

                    <dt class="col-sm-5">Dibuat pada</dt>
                    <dd class="col-sm-7">{{ $sesi->created_at?->format('d M Y H:i') ?? '-' }}</dd>

                    <dt class="col-sm-5">Terakhir diubah</dt>
                    <dd class="col-sm-7">{{ $sesi->updated_at?->format('d M Y H:i') ?? '-' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Siswa --}}
<div class="card shadow-sm">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h6 class="mb-0">
            <i class="fas fa-users me-2"></i>
            Daftar Siswa ({{ $sesi->students->count() }})
        </h6>
        <div class="btn-group" role="group">
            @if($availableStudents->count() > 0)
            <form method="POST" action="{{ route('admin.sesi-ujian.students.bulk', $sesi) }}" class="d-inline me-1">
                @csrf
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Tambahkan SEMUA {{ $availableStudents->count() }} siswa tersisa dari kelas ujian?')" title="Assign All Available">
                    <i class="fas fa-users-plus"></i> +{{ $availableStudents->count() }}
                </button>
            </form>
            @endif
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#tambahSiswaModal" title="Pilih Manual">
                <i class="fas fa-plus"></i> Pilih
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        @if($sesi->students->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Siswa</th>
                        <th width="120">Kelas</th>
                        <th width="100">Jenis Kelamin</th>
                        <th width="120">Status Ujian</th>
                        <th width="80">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sesi->students as $index => $student)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $student->name }}</strong>
                            @if($student->nis_nip)
                            <br><small class="text-muted">{{ $student->nis_nip }}</small>
                            @endif
                        </td>
                        <td>{{ $student->schoolClass->name ?? $student->kelas ?? ' - ' }}</td>
                        <td>
                            @if($student->jenis_kelamin == 'L')
                                <span class="badge bg-primary">Laki-laki</span>
                            @elseif($student->jenis_kelamin == 'P')
                                <span class="badge bg-pink">Perempuan</span>
                            @else
                                <span class="badge bg-secondary">-</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $result = $student->ujianResults->where('sesi_ujian_id', $sesi->id)->first();
                            @endphp
                            @if($result)
                                <span class="badge bg-success fs-6">Selesai ({{ $result->score ?? '0' }})</span>
                            @else
                                <span class="badge bg-warning fs-6">Belum</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.sesi-ujian.student.destroy', [$sesi, $student]) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin hapus {{ $student->name }} dari sesi ini?')" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 bg-light">
            <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
            <h5 class="text-muted mb-1">Belum ada siswa terdaftar</h5>
            <p class="text-muted mb-4">Gunakan tombol di atas untuk assign siswa dari kelas ujian</p>
            @if($availableStudents->count() > 0)
            <form method="POST" action="{{ route('admin.sesi-ujian.students.bulk', $sesi) }}" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('Assign SEMUA {{ $availableStudents->count() }} siswa dari kelas ujian?')">
                    <i class="fas fa-magic me-2"></i>Assign All {{ $availableStudents->count() }} Siswa Sekarang!
                </button>
            </form>
            @else
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>Semua siswa kelas sudah terdaftar atau tidak ada siswa di kelas ujian.
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

{{-- Modal Tambah Siswa --}}
<div class="modal fade" id="tambahSiswaModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title mb-0">
                    <i class="fas fa-user-plus me-2"></i>Tambah Siswa ke Sesi ({{ $availableStudents->count() }} tersedia)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.sesi-ujian.students.bulk', $sesi) }}">
                @csrf
                <div class="modal-body">
                    @if($availableStudents->count() > 0)
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllAvailable" checked>
                        <label class="form-check-label fw-bold" for="selectAllAvailable">
                            <i class="fas fa-check-double me-1 text-success"></i>Select All ({{ $availableStudents->count() }} siswa)
                        </label>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="50"><input type="checkbox" id="selectAllAvailable" checked></th>
                                    <th>Nama Siswa</th>
                                    <th width="120">Kelas</th>
                                    <th width="100">Jenis Kelamin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($availableStudents as $student)
                                <tr>
                                    <td><input class="form-check-input student-checkbox" type="checkbox" name="student_ids[]" value="{{ $student->id }}" checked></td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->schoolClass->name ?? $student->kelas ?? '-' }}</td>
                                    <td>
                                        @if($student->jenis_kelamin == 'L')
                                            <span class="badge bg-primary">Laki-laki</span>
                                        @elseif($student->jenis_kelamin == 'P')
                                            <span class="badge bg-pink">Perempuan</span>
                                        @else
                                            <span class="badge bg-secondary">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Tidak ada siswa tersedia (semua sudah terdaftar).
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Tambah {{ $availableStudents->count() }} Siswa Terpilih
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.bg-pink { 
    background-color: #e91e63 !important; 
    color: white !important;
}
.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.querySelector('#selectAllAvailable');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');

    selectAll.addEventListener('change', function() {
        studentCheckboxes.forEach(cb => cb.checked = this.checked);
    });

    studentCheckboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const allChecked = Array.from(studentCheckboxes).every(c => c.checked);
            selectAll.checked = allChecked;
        });
    });
});
</script>
@endpush
