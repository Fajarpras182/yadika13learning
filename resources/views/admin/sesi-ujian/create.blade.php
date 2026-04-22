@extends('layouts.app')

@section('title', 'Tambah Sesi Ujian - Admin')
@section('page-title', 'Tambah Sesi Ujian')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Buat Sesi Ujian Baru</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.sesi-ujian.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="ujian_id" class="form-label">Ujian <span class="text-danger">*</span></label>
                        <select name="ujian_id" id="ujian_id" class="form-select @error('ujian_id') is-invalid @enderror" required>
                            <option value="">Pilih Ujian...</option>
                            @foreach($ujians as $ujian)
                                <option value="{{ $ujian->id }}" {{ old('ujian_id') == $ujian->id ? 'selected' : '' }}>
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
                        <input type="text" name="nama_sesi" id="nama_sesi" class="form-control @error('nama_sesi') is-invalid @enderror" value="{{ old('nama_sesi') }}" placeholder="Contoh: Sesi Pagi" required>
                        @error('nama_sesi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="waktu_mulai" class="form-label">Waktu Mulai (WIB) <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="waktu_mulai" id="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai') }}" min="{{ now()->format('Y-m-d\TH:i') }}" required>
                                <small class="text-muted">Format: Tanggal dan jam dalam zona waktu Jakarta (WIB)</small>
                                @error('waktu_mulai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="waktu_selesai" class="form-label">Waktu Selesai (WIB) <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="waktu_selesai" id="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai') }}" required>
                                <small class="text-muted">Format: Tanggal dan jam dalam zona waktu Jakarta (WIB)</small>
                                @error('waktu_selesai')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="form-check-label">Aktif</label>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.sesi-ujian') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Sesi Ujian
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
