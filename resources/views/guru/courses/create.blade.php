@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran - Guru')
@section('page-title', 'Tambah Mata Pelajaran')

@section('content')
<div class="card shadow">
    <div class="card-header">
        <h5 class="mb-0">Form Tambah Mata Pelajaran</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('guru.courses.store') }}">
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
                        <label for="kelas" class="form-label">Kelas <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('kelas') is-invalid @enderror"
                               id="kelas" name="kelas" value="{{ old('kelas') }}" required>
                        @error('kelas')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jurusan" class="form-label">Jurusan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('jurusan') is-invalid @enderror"
                               id="jurusan" name="jurusan" value="{{ old('jurusan') }}" required>
                        @error('jurusan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                        <select class="form-select @error('semester') is-invalid @enderror"
                                id="semester" name="semester" required>
                            <option value="">Pilih Semester</option>
                            <option value="1" {{ old('semester') == '1' ? 'selected' : '' }}>1</option>
                            <option value="2" {{ old('semester') == '2' ? 'selected' : '' }}>2</option>
                            <option value="3" {{ old('semester') == '3' ? 'selected' : '' }}>3</option>
                            <option value="4" {{ old('semester') == '4' ? 'selected' : '' }}>4</option>
                            <option value="5" {{ old('semester') == '5' ? 'selected' : '' }}>5</option>
                            <option value="6" {{ old('semester') == '6' ? 'selected' : '' }}>6</option>
                        </select>
                        @error('semester')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="sks" class="form-label">SKS <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('sks') is-invalid @enderror"
                               id="sks" name="sks" value="{{ old('sks', 2) }}" min="1" max="6" required>
                        @error('sks')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                          id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
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
                <a href="{{ route('guru.courses') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
