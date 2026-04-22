@extends('layouts.app')

@section('title', 'Edit Mata Pelajaran - E-Learning SMK Yadika 13')
@section('page-title', 'Edit Mata Pelajaran')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Mata Pelajaran
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.courses.update', $course->id) }}">
                    @csrf
                    @method('PATCH')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="kode_mata_pelajaran" class="form-label">Kode Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('kode_mata_pelajaran') is-invalid @enderror"
                                   id="kode_mata_pelajaran" name="kode_mata_pelajaran"
                                   value="{{ old('kode_mata_pelajaran', $course->kode_mata_pelajaran) }}" required>
                            @error('kode_mata_pelajaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nama_mata_pelajaran" class="form-label">Nama Mata Pelajaran <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_mata_pelajaran') is-invalid @enderror"
                                   id="nama_mata_pelajaran" name="nama_mata_pelajaran"
                                   value="{{ old('nama_mata_pelajaran', $course->nama_mata_pelajaran) }}" required>
                            @error('nama_mata_pelajaran')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="guru_id" class="form-label">Guru Mata Pelajaran <span class="text-danger">*</span></label>
                            <select class="form-select @error('guru_id') is-invalid @enderror" id="guru_id" name="guru_id" required>
                                <option value="">Pilih Guru</option>
                                @foreach($gurus as $guru)
                                    <option value="{{ $guru->id }}" {{ old('guru_id', $course->guru_id) == $guru->id ? 'selected' : '' }}>
                                        {{ $guru->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('guru_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="class_id" class="form-label">Kelas <span class="text-danger">*</span></label>
                            <select class="form-select @error('class_id') is-invalid @enderror"
                                    id="class_id" name="class_id" required>
                                <option value="">Pilih Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ old('class_id', $course->class_id) == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('class_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="major_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                            <select class="form-select @error('major_id') is-invalid @enderror"
                                    id="major_id" name="major_id" required>
                                <option value="">Pilih Jurusan</option>
                                @foreach($majors as $major)
                                    <option value="{{ $major->id }}" {{ old('major_id', $course->major_id) == $major->id ? 'selected' : '' }}>
                                        {{ $major->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('major_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
                            <select class="form-select @error('semester') is-invalid @enderror" id="semester" name="semester" required>
                                <option value="">Pilih Semester</option>
                                <option value="Ganjil" {{ old('semester', $course->semester) == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                                <option value="Genap" {{ old('semester', $course->semester) == 'Genap' ? 'selected' : '' }}>Genap</option>
                            </select>
                            @error('semester')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.courses') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
