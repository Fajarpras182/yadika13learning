@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-edit me-2"></i>Edit Kelas
                </h6>
                <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.classes.update', $class) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Kelas <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $class->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="major_id" class="form-label">Jurusan <span class="text-danger">*</span></label>
                                <select class="form-select @error('major_id') is-invalid @enderror"
                                        id="major_id" name="major_id" required>
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($majors as $major)
                                        <option value="{{ $major->id }}" {{ old('major_id', $class->major_id) == $major->id ? 'selected' : '' }}>
                                            {{ $major->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('major_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="year" class="form-label">Tahun Ajaran</label>
                                <input type="number" class="form-control @error('year') is-invalid @enderror"
                                       id="year" name="year" value="{{ old('year', $class->year) }}"
                                       min="2000" max="2100" placeholder="2024">
                                @error('year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="homeroom_teacher" class="form-label">Wali Kelas</label>
                                <input type="text" class="form-control @error('homeroom_teacher') is-invalid @enderror"
                                       id="homeroom_teacher" name="homeroom_teacher"
                                       value="{{ old('homeroom_teacher', $class->homeroom_teacher) }}"
                                       placeholder="Nama Wali Kelas">
                                @error('homeroom_teacher')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active"
                                   name="is_active" value="1" {{ old('is_active', $class->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Kelas Aktif
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
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
