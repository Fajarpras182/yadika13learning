@extends('layouts.app')

@section('title', 'Tambah Soal ke Ujian')
@section('page-title', 'Tambah Soal ke Ujian')

@section('content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Tambah Soal ke Ujian: {{ $ujian->judul }}</h5>
            <small class="text-muted">Mata Pelajaran: {{ $ujian->course->nama_mata_pelajaran ?? '-' }}</small>
        </div>
        <a href="{{ route('admin.ujian.show', $ujian->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-check-circle me-2"></i>Soal di Ujian ({{ $ujianQuestions->count() }})</h6>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @if($ujianQuestions->count() > 0)
                            @foreach($ujianQuestions as $question)
                                <div class="mb-3 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <strong>Pertanyaan:</strong>
                                            <div class="mb-2">{!! Str::limit(strip_tags($question->pertanyaan), 100) !!}</div>
                                            <small class="text-muted">
                                                Kunci: {{ strtoupper($question->kunci_jawaban) }}
                                            </small>
                                        </div>
                                        <button class="btn btn-sm btn-outline-danger ms-2" onclick="removeQuestion({{ $question->id }}, '{{ addslashes($question->pertanyaan) }}')" title="Hapus dari Ujian">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-3">Belum ada soal di ujian ini</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fas fa-database me-2"></i>Soal di Bank Soal ({{ $availableQuestions->count() }})</h6>
                    </div>
                    <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                        @if($availableQuestions->count() > 0)
                            <form id="addQuestionsForm" method="POST" action="{{ route('admin.ujian.store-questions', $ujian->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-success btn-sm" id="addSelectedBtn" disabled>
                                        <i class="fas fa-plus me-1"></i>Tambah Soal Terpilih
                                    </button>
                                </div>

                                @foreach($availableQuestions as $question)
                                    <div class="mb-3 p-3 border rounded">
                                        <div class="form-check">
                                            <input class="form-check-input question-checkbox" type="checkbox"
                                                   name="question_ids[]" value="{{ $question->id }}"
                                                   id="question_{{ $question->id }}">
                                            <label class="form-check-label" for="question_{{ $question->id }}">
                                                <strong>Pertanyaan:</strong>
                                                <div class="mb-2">{!! Str::limit(strip_tags($question->pertanyaan), 100) !!}</div>
                                                <small class="text-muted">
                                                    Kunci: {{ strtoupper($question->kunci_jawaban) }}
                                                </small>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </form>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted">Tidak ada soal tersedia di bank soal</p>
                                <a href="{{ route('admin.bank-soal.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i>Buat Soal Baru
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('admin.ujian.show', $ujian->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Ujian
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.question-checkbox');
    const addSelectedBtn = document.getElementById('addSelectedBtn');

    function updateAddButton() {
        const checkedBoxes = document.querySelectorAll('.question-checkbox:checked');
        addSelectedBtn.disabled = checkedBoxes.length === 0;
        addSelectedBtn.innerHTML = '<i class="fas fa-plus me-1"></i>Tambah ' + checkedBoxes.length + ' Soal Terpilih';
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateAddButton);
    });

    // Initial check
    updateAddButton();
});

function removeQuestion(questionId, questionText) {
    const truncatedText = questionText.length > 50 ? questionText.substring(0, 50) + '...' : questionText;

    if (confirm('Yakin ingin menghapus soal ini dari ujian?\n\n"' + truncatedText + '"\n\nSoal akan dikembalikan ke bank soal.')) {
        // Create form for DELETE request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/ujian/{{ $ujian->id }}/remove-question/${questionId}`;

        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);

        // Add method spoofing for DELETE
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
