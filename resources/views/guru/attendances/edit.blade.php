@extends('layouts.app')

@section('title', 'Edit Absensi - ' . $course->nama_mata_pelajaran . ' - E-Learning SMK Yadika 13')
@section('page-title', 'Edit Absensi - ' . $course->nama_mata_pelajaran)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.attendances', $course->id) }}">Absensi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
</nav>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>Edit Absensi: {{ $attendance->tanggal->format('d/m/Y') }}
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('guru.attendances.update', [$course->id, $attendance->id]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">Tanggal Absensi <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('tanggal') is-invalid @enderror"
                                       id="tanggal" name="tanggal" value="{{ old('tanggal', $attendance->tanggal->format('Y-m-d')) }}" required>
                                @error('tanggal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Nama Siswa <span class="text-danger">*</span></label>
                                <select class="form-select @error('student_id') is-invalid @enderror"
                                        id="student_id" name="student_id" required>
                                    <option value="">Pilih Siswa</option>
                                    @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id', $attendance->student_id) == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->nis_nip }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status Kehadiran <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status" name="status" required>
                            <option value="hadir" {{ old('status', $attendance->status) == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="izin" {{ old('status', $attendance->status) == 'izin' ? 'selected' : '' }}>Izin</option>
                            <option value="sakit" {{ old('status', $attendance->status) == 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="alpa" {{ old('status', $attendance->status) == 'alpa' ? 'selected' : '' }}>Alpa</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control @error('keterangan') is-invalid @enderror"
                                  id="keterangan" name="keterangan" rows="3">{{ old('keterangan', $attendance->keterangan) }}</textarea>
                        @error('keterangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('guru.attendances', $course->id) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Update Absensi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
