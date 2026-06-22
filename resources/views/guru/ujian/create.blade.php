@extends('layouts.app')

@section('title', 'Buat Ujian Baru - Guru')
@section('page-title', 'Buat Ujian Baru')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="h4 mb-1"><i class="fas fa-file-circle-plus me-2 text-primary"></i>Buat ujian baru</h2>
        <p class="text-muted small mb-0">Isi data di bawah. Jadwal mulai diset otomatis 7 hari ke depan; Anda dapat mengubahnya di halaman edit.</p>
    </div>
    <a href="{{ route('guru.ujian') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form id="form-buat-ujian" method="POST" action="{{ route('guru.ujian.store') }}">
            @csrf
            <input type="hidden" name="tanggal_ujian" value="{{ old('tanggal_ujian', $defaultTanggalUjian) }}">
            <input type="hidden" name="bobot_nilai" value="{{ old('bobot_nilai', 100) }}">
            <input type="hidden" name="is_active" value="1">

            <div class="mb-3">
                <label for="judul" class="form-label">Judul ujian <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('judul') is-invalid @enderror"
                       id="judul" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Ujian Tengah Semester">
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="course_id" class="form-label">Mata pelajaran <span class="text-danger">*</span></label>
                    <select class="form-select @error('course_id') is-invalid @enderror" id="course_id" name="course_id" required>
                        <option value="">Pilih mata pelajaran</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ (string) old('course_id') === (string) $course->id ? 'selected' : '' }}>
                                {{ $course->nama_mata_pelajaran }} — {{ $course->kelas }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="class_ids" class="form-label">Kelas <span class="text-danger">*</span></label>
                    <select class="form-select @error('class_ids') is-invalid @enderror" id="class_ids" name="class_ids[]" multiple required size="6">
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ in_array($class->id, old('class_ids', [])) ? 'selected' : '' }}>
                                {{ $class->name ?? $class->kelas }}
                            </option>
                        @endforeach
                    </select>
                    <div class="form-text">Tahan Ctrl (Windows) atau Cmd (Mac) untuk memilih lebih dari satu kelas.</div>
                    @error('class_ids')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('class_ids.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label class="form-label d-block">Acak soal</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="soal_acak" id="soal_acak_yes" value="1" {{ old('soal_acak', '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="soal_acak_yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="soal_acak" id="soal_acak_no" value="0" {{ old('soal_acak', '0') == '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="soal_acak_no">No</label>
                    </div>
                    @error('soal_acak')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label d-block">Acak jawaban</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jawaban_acak" id="jawaban_acak_yes" value="1" {{ old('jawaban_acak', '0') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="jawaban_acak_yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="jawaban_acak" id="jawaban_acak_no" value="0" {{ old('jawaban_acak', '0') == '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="jawaban_acak_no">No</label>
                    </div>
                    @error('jawaban_acak')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label d-block">Tampilkan hasil</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tampilkan_hasil" id="tampilkan_hasil_yes" value="1" {{ old('tampilkan_hasil', '1') == '1' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tampilkan_hasil_yes">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="tampilkan_hasil" id="tampilkan_hasil_no" value="0" {{ old('tampilkan_hasil', '1') == '0' ? 'checked' : '' }}>
                        <label class="form-check-label" for="tampilkan_hasil_no">No</label>
                    </div>
                    @error('tampilkan_hasil')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label for="durasi_menit" class="form-label">Durasi (menit) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control @error('durasi_menit') is-invalid @enderror"
                           id="durasi_menit" name="durasi_menit" value="{{ old('durasi_menit', 60) }}" min="1" required>
                    @error('durasi_menit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex flex-wrap gap-2 mt-4 pt-3 border-top">
                <button type="submit" class="btn btn-primary" title="Simpan">
                    <i class="fas fa-save"></i><span class="ms-2 d-none d-sm-inline">Simpan</span>
                </button>
                <button type="reset" class="btn btn-outline-secondary" title="Reset form">
                    <i class="fas fa-rotate-left"></i><span class="ms-2 d-none d-sm-inline">Reset</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('form-buat-ujian')?.addEventListener('reset', function () {
    setTimeout(function () {
        document.querySelectorAll('input[name="soal_acak"][value="0"]').forEach(function (el) { el.checked = true; });
        document.querySelectorAll('input[name="jawaban_acak"][value="0"]').forEach(function (el) { el.checked = true; });
        document.querySelectorAll('input[name="tampilkan_hasil"][value="1"]').forEach(function (el) { el.checked = true; });
        var d = document.querySelector('input[name="durasi_menit"]');
        if (d) d.value = '60';
    }, 0);
});
</script>
@endpush
