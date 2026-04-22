@extends('layouts.app')

@section('title', 'Detail Ujian - Admin')
@section('page-title', 'Detail Ujian')

@section('content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Detail Ujian</h5>
        <a href="{{ route('admin.ujian') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        <dl class="row">
            <dt class="col-sm-3">Judul Ujian</dt>
            <dd class="col-sm-9">{{ $ujian->judul }}</dd>

            <dt class="col-sm-3">Mata Pelajaran</dt>
            <dd class="col-sm-9">{{ $ujian->course->nama_mata_pelajaran ?? '-' }}</dd>

            <dt class="col-sm-3">Kelas Peserta</dt>
            <dd class="col-sm-9">
                @if($classes->count() > 0)
                    <ul class="list-unstyled mb-0">
                        @foreach($classes as $class)
                            <li>{{ $class->name ?? $class->kelas }}</li>
                        @endforeach
                    </ul>
                @else
                    -
                @endif
            </dd>

            <dt class="col-sm-3">Tanggal & Waktu</dt>
            <dd class="col-sm-9">{{ $ujian->tanggal_ujian->format('d/m/Y H:i') }}</dd>

            <dt class="col-sm-3">Waktu Pengerjaan</dt>
            <dd class="col-sm-9">{{ $ujian->durasi_menit }} menit</dd>

            <dt class="col-sm-3">Bobot Nilai</dt>
            <dd class="col-sm-9">{{ $ujian->bobot_nilai }}%</dd>

            <dt class="col-sm-3">Acak Soal</dt>
            <dd class="col-sm-9">{{ $ujian->soal_acak ? 'Yes' : 'No' }}</dd>

            <dt class="col-sm-3">Acak Jawaban</dt>
            <dd class="col-sm-9">{{ ($ujian->jawaban_acak ?? false) ? 'Yes' : 'No' }}</dd>

            <dt class="col-sm-3">Tampilkan Hasil</dt>
            <dd class="col-sm-9">{{ ($ujian->tampilkan_hasil ?? true) ? 'Yes' : 'No' }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">
                @if($ujian->is_active)
                    <span class="badge bg-success">Aktif</span>
                @else
                    <span class="badge bg-warning">Draft</span>
                @endif
            </dd>

            <dt class="col-sm-3">Deskripsi</dt>
            <dd class="col-sm-9">{{ $ujian->deskripsi ?? '-' }}</dd>
        </dl>
    </div>
    <div class="card-footer d-flex justify-content-between align-items-center">
        <div>
            <strong>Soal:</strong> {{ $ujian->questions->count() }} tersedia
            @if($ujian->questions->count() == 0)
                <br><small class="text-muted">Tambah soal lewat <strong>Kelola bank soal</strong> (tambah manual atau impor file).</small>
            @endif
        </div>
        <div>
            <a href="{{ route('admin.bank-soal', ['ujian_id' => $ujian->id, 'course_id' => $ujian->course_id]) }}" class="btn btn-success">
                <i class="fas fa-database me-1"></i> Kelola bank soal
            </a>
        </div>
    </div>
</div>
@endsection
