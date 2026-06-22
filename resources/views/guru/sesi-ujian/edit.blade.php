@extends('layouts.app')

@section('title', 'Edit Sesi Ujian - Guru')
@section('page-title', 'Edit Sesi Ujian')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Edit Sesi Ujian: {{ $sesi->nama_sesi }}
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('guru.sesi-ujian.update', $sesi->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="ujian_id" class="form-label">Ujian <span class="text-danger">*</span></label>
                        <select name="ujian_id" id="ujian_id" class="form-select @error('ujian_id') is-invalid @enderror" required>
                            <option value="">Pilih Ujian...</option>
                            @foreach($ujians as $ujian)
                                <option value="{{ $ujian->id }}" {{ old('ujian_id', $sesi->ujian_id) == $ujian->id ? 'selected' : '' }}>
                                    {{ $ujian->judul }} ({{ $ujian->course->nama_mata_pelajaran ?? '-' }})
                                </option>
                            @endforeach
                        </select>
                        @error('ujian_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="nama_sesi" class="form-label">Nama Sesi <span class="text-danger">*</span></label>
                        <input type="text" name="nama_sesi" id="nama_sesi" class="form-control @error('nama_sesi') is-invalid @enderror" value="{{ old('nama_sesi', $sesi->nama_sesi) }}" required>
                        @error('nama_sesi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="waktu_mulai" class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="waktu_mulai" id="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai', $sesi->waktu_mulai?->format('Y-m-d\TH:i')) }}" required>
                                @error('waktu_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="waktu_selesai" class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="waktu_selesai" id="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai', $sesi->waktu_selesai?->format('Y-m-d\TH:i')) }}" required>
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', $sesi->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Aktif</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('guru.sesi-ujian') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Update Sesi Ujian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

