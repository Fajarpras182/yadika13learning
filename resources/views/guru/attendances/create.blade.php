@extends('layouts.app')

@section('title', 'Tambah Absensi - ' . $course->nama_mata_pelajaran . ' - E-Learning SMK Yadika 13')
@section('page-title', 'Tambah Absensi - ' . $course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.attendances', $course->id) }}">Absensi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah</li>
    </ol>
</nav>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Absensi Siswa
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('guru.attendances.store', $course->id) }}" id="attendanceForm">
                    @csrf

                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Tanggal Absensi <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                               id="tanggal" name="tanggal" value="{{ old('tanggal', date('Y-m-d')) }}" required>
                        @error('tanggal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <h6>Daftar Siswa</h6>
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr class="table-primary">
                                        <th width="5%"><input type="checkbox" id="selectAll"></th>
                                        <th>Nama Siswa</th>
                                        <th>NIS</th>
                                        <th>Status Kehadiran</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody id="studentTableBody">
                                    @if($students->count() > 0)
                                        @foreach($students as $student)
                                        <tr data-student-id="{{ $student->id }}">
                                            <td><input type="checkbox" class="student-checkbox" value="{{ $student->id }}"></td>
                                            <td>{{ $student->name }}</td>
                                            <td>{{ $student->nis_nip }}</td>
                                            <td>
                                                <select class="form-select status-select"
                                                        name="attendances[{{ $student->id }}][status]" required>
                                                    <option value="hadir">Hadir</option>
                                                    <option value="izin">Izin</option>
                                                    <option value="sakit">Sakit</option>
                                                    <option value="alpa">Alpa</option>
                                                </select>
                                                <input type="hidden" name="attendances[{{ $student->id }}][student_id]" value="{{ $student->id }}">
                                            </td>
                                            <td>
                                                <textarea class="form-control" name="attendances[{{ $student->id }}][keterangan]" rows="1" placeholder="Opsional"></textarea>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">
                                                <i class="fas fa-info-circle me-2"></i>
                                                Tidak ada siswa di kelas ini atau mata pelajaran belum dihubungkan dengan kelas.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @error('attendances')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('guru.attendances', $course->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Simpan Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');

    // Handle select all checkbox
    selectAllCheckbox.addEventListener('change', function() {
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all checkbox when individual checkboxes change
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.student-checkbox:checked');
            selectAllCheckbox.checked = checkedBoxes.length === studentCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < studentCheckboxes.length;
        });
    });
});
</script>
@endsection
