@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran - Admin')
@section('page-title', 'Tambah Mata Pelajaran')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h5 class="mb-0">Form Tambah Mata Pelajaran</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.courses.store') }}">
            @csrf

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="kode_mata_pelajaran" class="form-label">Kode Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kode_mata_pelajaran') is-invalid @enderror"
                               id="kode_mata_pelajaran" name="kode_mata_pelajaran"
                               value="{{ old('kode_mata_pelajaran') }}" required>
                        @error('kode_mata_pelajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nama_mata_pelajaran" class="form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nama_mata_pelajaran') is-invalid @enderror"
                               id="nama_mata_pelajaran" name="nama_mata_pelajaran"
                               value="{{ old('nama_mata_pelajaran') }}" required>
                        @error('nama_mata_pelajaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="guru_id" class="form-label">Guru Mata Pelajaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('guru_id') is-invalid @enderror"
                                id="guru_id" name="guru_id" required>
                            <option value="">Pilih Guru</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('guru_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select class="form-select @error('class_id') is-invalid @enderror"
                                id="class_id" name="class_id" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="major_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                        <select class="form-select @error('major_id') is-invalid @enderror"
                                id="major_id" name="major_id" required>
                            <option value="">Pilih Jurusan</option>
                            @foreach($majors as $major)
                                <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                    {{ $major->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('major_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select @error('semester') is-invalid @enderror"
                                id="semester" name="semester" required>
                            <option value="">Pilih Semester</option>
                            <option value="Ganjil" {{ old('semester') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ old('semester') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>



            <div class="mb-3">
                <div class="form-check">
                    <input type="hidden" name="is_active" value="0">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">
                        Aktif
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan
                </button>
                <a href="{{ route('admin.courses') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
