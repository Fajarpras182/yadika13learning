@extends('layouts.app')

@section('title', 'Edit Ujian - Guru')
@section('page-title', 'Edit Ujian')

@section('content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Form Edit Ujian</h5>
            <small class="text-muted">Edit detail ujian yang sudah dibuat</small>
        </div>
        <button class="btn btn-danger btn-sm" onclick="confirmDelete()">
            <i class="fas fa-trash me-1"></i> Hapus Ujian
        </button>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('guru.ujian.update', $ujian->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Ujian <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('judul') is-invalid @enderror"
                               id="judul" name="judul"
                               value="{{ old('judul', $ujian->judul) }}" required>
                        @error('judul')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('course_id') is-invalid @enderror"
                                id="course_id" name="course_id" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (old('course_id', $ujian->course_id) == $course->id) ? 'selected' : '' }}>
                                    {{ $course->nama_mata_pelajaran }} - {{ $course->kelas }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="class_ids" class="form-label">Kelas Peserta Ujian <span class="text-danger">*</span></label>
                        <select class="form-select @error('class_ids') is-invalid @enderror"
                                id="class_ids" name="class_ids[]" multiple required>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ in_array($class->id, old('class_ids', $ujian->class_ids ?? [])) ? 'selected' : '' }}>
                                    {{ $class->name ?? $class->kelas }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Pilih satu atau lebih kelas yang akan mengikuti ujian.</div>
                        @error('class_ids')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        @error('class_ids.*')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="tanggal_ujian" class="form-label">Tanggal & Waktu Ujian <span class="text-danger">*</span></label>
                        <input type="datetime-local" class="form-control @error('tanggal_ujian') is-invalid @enderror"
                               id="tanggal_ujian" name="tanggal_ujian"
                               value="{{ old('tanggal_ujian', $ujian->tanggal_ujian->format('Y-m-d\TH:i')) }}" required>
                        @error('tanggal_ujian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="durasi_menit" class="form-label">Waktu Pengerjaan (Menit) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('durasi_menit') is-invalid @enderror"
                               id="durasi_menit" name="durasi_menit"
                               value="{{ old('durasi_menit', $ujian->durasi_menit) }}" min="1" required>
                        @error('durasi_menit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="bobot_nilai" class="form-label">Bobot Nilai (%) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('bobot_nilai') is-invalid @enderror"
                               id="bobot_nilai" name="bobot_nilai"
                               value="{{ old('bobot_nilai', $ujian->bobot_nilai) }}" min="1" max="100" required>
                        @error('bobot_nilai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Acak Soal</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="soal_acak" id="soal_acak_yes" value="1" {{ old('soal_acak', $ujian->soal_acak ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="soal_acak_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="soal_acak" id="soal_acak_no" value="0" {{ old('soal_acak', $ujian->soal_acak ? '1' : '0') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="soal_acak_no">No</label>
                        </div>
                        @error('soal_acak')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Acak Jawaban</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jawaban_acak" id="jawaban_acak_yes" value="1" {{ old('jawaban_acak', ($ujian->jawaban_acak ?? false) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jawaban_acak_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jawaban_acak" id="jawaban_acak_no" value="0" {{ old('jawaban_acak', ($ujian->jawaban_acak ?? false) ? '1' : '0') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="jawaban_acak_no">No</label>
                        </div>
                        @error('jawaban_acak')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Tampilkan Hasil</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tampilkan_hasil" id="tampilkan_hasil_yes" value="1" {{ old('tampilkan_hasil', ($ujian->tampilkan_hasil ?? true) ? '1' : '0') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tampilkan_hasil_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tampilkan_hasil" id="tampilkan_hasil_no" value="0" {{ old('tampilkan_hasil', ($ujian->tampilkan_hasil ?? true) ? '1' : '0') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tampilkan_hasil_no">No</label>
                        </div>
                        @error('tampilkan_hasil')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi</label>
                <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                          id="deskripsi" name="deskripsi" rows="3">{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ (old('is_active', $ujian->is_active) ? 'checked' : '') }}>
                    <label class="form-check-label" for="is_active">
                        Aktif
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <button type="submit" class="btn btn-primary" title="Simpan perubahan">
                    <i class="fas fa-save"></i>
                </button>
                <button type="reset" class="btn btn-outline-secondary" title="Reset form">
                    <i class="fas fa-undo-alt"></i>
                </button>
                <a href="{{ route('guru.ujian') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus Ujian</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus ujian <strong>"{{ $ujian->judul }}"</strong>?</p>
                <div class="alert alert-warning">
                    <strong>Perhatian:</strong> Ujian yang sudah dihapus tidak dapat dikembalikan. Pastikan ujian tidak memiliki soal yang masih terhubung.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('guru.ujian.delete', $ujian->id) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Ujian</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete() {
    $('#deleteModal').modal('show');
}
</script>
@endpush