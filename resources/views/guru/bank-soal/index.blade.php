@extends('layouts.app')

@section('title', 'Manajemen Bank Soal - Guru')
@section('page-title', 'Manajemen Bank Soal')

@push('styles')
<style>
    .bank-soal-page-title {
        font-size: 1.35rem;
        font-weight: 600;
        letter-spacing: -0.02em;
        color: #1a1d24;
    }
    .bank-soal-toolbar .btn-tambah {
        background: #12151c;
        border-color: #12151c;
        color: #fff;
        font-weight: 500;
        padding: 0.5rem 1.1rem;
    }
    .bank-soal-toolbar .btn-tambah:hover {
        background: #2a2f3d;
        border-color: #2a2f3d;
        color: #fff;
    }
    .bank-soal-toolbar .btn-import {
        font-weight: 500;
        padding: 0.5rem 1.1rem;
    }
    .bank-soal-table-wrap {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        overflow: hidden;
        background: #fff;
    }
    .bank-soal-table {
        margin-bottom: 0;
        --bs-table-border-color: #dee2e6;
    }
    .bank-soal-table thead th {
        background: #12151c !important;
        color: #fff !important;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        border: none;
        padding: 0.85rem 1rem;
        vertical-align: middle;
    }
    .bank-soal-table tbody td {
        padding: 1rem 1rem;
        vertical-align: middle;
        border-color: #dee2e6;
    }
    .bank-soal-table .col-no {
        width: 4.5rem;
        text-align: center;
        font-weight: 600;
        font-size: 1rem;
        color: #1a1d24;
        background: #fafbfc;
    }
    .bank-soal-pertanyaan {
        font-size: 0.95rem;
        line-height: 1.55;
        color: #212529;
    }
    .bank-soal-meta {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }
    .bank-soal-options {
        margin-top: 0.85rem;
        font-size: 0.92rem;
        line-height: 1.5;
    }
    .bank-soal-option {
        padding: 0.12rem 0;
    }
    .bank-soal-option.is-key {
        color: #198754 !important;
        font-weight: 600;
    }
    .bank-soal-actions {
        margin-top: 0.85rem;
        padding-top: 0.65rem;
        border-top: 1px solid #e9ecef;
        font-size: 0.85rem;
    }
</style>
@endpush

@section('content')
<div class="mb-3 pb-2 border-bottom">
    <h2 class="bank-soal-page-title mb-0">
        <i class="fas fa-list-check me-2 text-secondary"></i>Soal Ujian
    </h2>
</div>

@php
    $bankCtxUjian = $contextUjian ?? null;
    $bankCtxQuery = array_filter(['ujian_id' => request('ujian_id'), 'course_id' => request('course_id')], fn ($v) => $v !== null && $v !== '');
@endphp

@if($bankCtxUjian)
<div class="alert alert-primary border-0 d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div class="small">
        <strong>Mode ujian:</strong> {{ $bankCtxUjian->judul }}
        <span class="text-muted">·</span> Soal pada ujian ini: <strong>{{ $bankCtxUjian->questions()->count() }}</strong>
        <span class="text-muted">·</span> {{ $bankCtxUjian->course->nama_mata_pelajaran ?? '-' }}
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('guru.ujian.show', $bankCtxUjian) }}" class="btn btn-sm btn-outline-dark rounded-0">Detail Ujian</a>
        <a href="{{ route('guru.bank-soal') }}" class="btn btn-sm btn-outline-secondary rounded-0">Keluar mode ujian</a>
    </div>
</div>
@endif

<div class="d-flex flex-wrap align-items-center gap-2 bank-soal-toolbar mb-3">
    <a href="{{ route('guru.bank-soal.create', $bankCtxQuery) }}" class="btn btn-tambah rounded-0">
        <i class="fas fa-plus-circle me-2"></i>Tambah
    </a>
    <button type="button" class="btn btn-success btn-import rounded-0" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="fas fa-file-import me-2"></i>Import
    </button>
    <a href="{{ asset('storage/template_soal.csv') }}" class="btn btn-outline-secondary btn-sm rounded-0" download>
        <i class="fas fa-download me-1"></i>Template CSV
    </a>
</div>

@if($questions->count() > 0)
<div class="bank-soal-table-wrap shadow-sm">
    <div class="table-responsive">
        <table class="table bank-soal-table align-middle">
            <thead>
                <tr>
                    <th class="col-no">No.</th>
                    <th>Soal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($questions as $index => $question)
                    @php
                        $key = strtolower((string) ($question->kunci_jawaban ?? ''));
                    @endphp
                    <tr>
                        <td class="col-no">{{ $index + 1 }}</td>
                        <td>
                            <div class="bank-soal-meta">
                                {{ $question->course->nama_mata_pelajaran ?? '-' }}
                                @if($bankCtxUjian && (int) $question->ujian_id === (int) $bankCtxUjian->id)
                                    <span class="badge bg-primary ms-1">Di ujian ini</span>
                                @elseif($question->ujian_id)
                                    <span class="badge bg-secondary ms-1">Ujian lain</span>
                                @else
                                    <span class="badge bg-light text-dark border ms-1">Bank</span>
                                @endif
                                @if(!$question->is_active)
                                    <span class="badge bg-secondary ms-1">Nonaktif</span>
                                @endif
                            </div>
                            <div class="bank-soal-pertanyaan">{!! $question->pertanyaan !!}</div>
                            <div class="bank-soal-options">
                                <div class="bank-soal-option {{ $key === 'a' ? 'is-key' : '' }}">A. {{ strip_tags($question->jawaban_a) }}</div>
                                <div class="bank-soal-option {{ $key === 'b' ? 'is-key' : '' }}">B. {{ strip_tags($question->jawaban_b) }}</div>
                                <div class="bank-soal-option {{ $key === 'c' ? 'is-key' : '' }}">C. {{ strip_tags($question->jawaban_c) }}</div>
                                <div class="bank-soal-option {{ $key === 'd' ? 'is-key' : '' }}">D. {{ strip_tags($question->jawaban_d) }}</div>
                                <div class="bank-soal-option {{ $key === 'e' ? 'is-key' : '' }}">E. {{ strip_tags($question->jawaban_e) }}</div>
                            </div>
                            <div class="bank-soal-actions d-flex flex-wrap gap-2 align-items-center">
                                <button type="button" class="btn btn-link btn-sm text-secondary p-0 text-decoration-none" onclick="viewQuestion({{ $question->id }})" title="Detail">
                                    <i class="fas fa-eye me-1"></i>Detail
                                </button>
                                <span class="text-muted">|</span>
                                <button type="button" class="btn btn-link btn-sm text-primary p-0 text-decoration-none" onclick="editQuestion({{ $question->id }})">
                                    <i class="fas fa-edit me-1"></i>Edit
                                </button>
                                <span class="text-muted">|</span>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none" onclick="confirmDelete({{ $question->id }})">
                                    <i class="fas fa-trash me-1"></i>Hapus
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@else
<div class="text-center py-5 border rounded bg-white">
    <i class="fas fa-database fa-3x text-muted mb-3"></i>
    <h4 class="text-muted">Belum ada soal dalam bank soal</h4>
    <p class="text-muted">Tambah atau import soal untuk memulai</p>
    <div class="d-flex justify-content-center gap-2 flex-wrap">
        <a href="{{ route('guru.bank-soal.create', $bankCtxQuery) }}" class="btn btn-dark rounded-0">
            <i class="fas fa-plus-circle me-2"></i>Tambah
        </a>
        <button type="button" class="btn btn-success rounded-0" data-bs-toggle="modal" data-bs-target="#uploadModal">
            <i class="fas fa-file-import me-2"></i>Import
        </button>
        <a href="{{ asset('storage/template_soal.csv') }}" class="btn btn-outline-secondary rounded-0" download>
            <i class="fas fa-download me-2"></i>Template
        </a>
    </div>
</div>
@endif

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-0">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="uploadModalLabel">Import soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('guru.bank-soal.upload') }}" enctype="multipart/form-data">
                @csrf
                @if($bankCtxUjian)
                    <input type="hidden" name="ujian_id" value="{{ $bankCtxUjian->id }}">
                @endif
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="upload_course_id" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select class="form-select" id="upload_course_id" name="course_id" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach(\App\Models\Course::with('schoolClass')->where('guru_id', auth()->id())->orderBy('nama_mata_pelajaran')->get() as $course)
                                <option value="{{ $course->id }}" @selected((string) old('course_id', $bankCtxUjian->course_id ?? '') === (string) $course->id)>{{ $course->nama_mata_pelajaran }}@if($course->schoolClass) — {{ $course->schoolClass->name }}@endif</option>
                            @endforeach
                        </select>
                    </div>
                    <p class="small text-muted">{{ \App\Services\BankSoalDocumentImport::supportedExtensionsMessage() }}</p>
                    <div class="mb-3">
                        <label for="file" class="form-label">File <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="file" name="file" accept=".xlsx,.xls,.csv,.docx" required>
                    </div>
                </div>
                <div class="modal-footer border-top">
                    <button type="button" class="btn btn-outline-secondary rounded-0" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-0">Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="questionModal" tabindex="-1" aria-labelledby="questionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="questionModalLabel">Detail Soal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="questionContent"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Yakin ingin menghapus soal ini? Soal yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/guru/bank-soal/${id}`;
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function viewQuestion(id) {
    fetch(`/guru/bank-soal/${id}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const question = data.question;
            let content = `
                <div class="mb-3">
                    <strong>Mata Pelajaran:</strong> ${question.course?.nama_mata_pelajaran || '-'}
                </div>
                <div class="mb-3">
                    <strong>Pertanyaan:</strong>
                    <div class="mt-2 p-3 bg-light rounded">${question.pertanyaan}</div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Jawaban A:</strong>
                            <div class="mt-2 p-2 border rounded">${question.jawaban_a}</div>
                        </div>
                        <div class="mb-3">
                            <strong>Jawaban B:</strong>
                            <div class="mt-2 p-2 border rounded">${question.jawaban_b}</div>
                        </div>
                        <div class="mb-3">
                            <strong>Jawaban C:</strong>
                            <div class="mt-2 p-2 border rounded">${question.jawaban_c}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Jawaban D:</strong>
                            <div class="mt-2 p-2 border rounded">${question.jawaban_d}</div>
                        </div>
                        <div class="mb-3">
                            <strong>Jawaban E:</strong>
                            <div class="mt-2 p-2 border rounded">${question.jawaban_e}</div>
                        </div>
                        <div class="mb-3">
                            <strong>Kunci Jawaban:</strong>
                            <span class="badge bg-success fs-6 ms-2">${question.kunci_jawaban.toUpperCase()}</span>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <strong>Status:</strong>
                    <span class="badge ${question.is_active ? 'bg-success' : 'bg-secondary'} ms-2">
                        ${question.is_active ? 'Aktif' : 'Nonaktif'}
                    </span>
                </div>
                <div class="mb-3">
                    <strong>Dibuat:</strong> ${new Date(question.created_at).toLocaleDateString('id-ID')}
                </div>
            `;
            content += `
                <div class="d-flex gap-2 mt-4">
                    <a href="/guru/bank-soal/${id}/edit" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Edit Soal
                    </a>
                    <button onclick="confirmDelete(${id})" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Hapus Soal
                    </button>
                </div>
            `;
            $('#questionContent').html(content);
            $('#questionModal').modal('show');
        } else {
            $('#questionContent').html('<div class="alert alert-danger">Gagal memuat detail soal.</div>');
            $('#questionModal').modal('show');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        $('#questionContent').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>');
        $('#questionModal').modal('show');
    });
}

function editQuestion(id) {
    window.location.href = `/guru/bank-soal/${id}/edit`;
}
</script>
@endpush
