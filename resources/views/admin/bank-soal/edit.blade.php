@extends('layouts.app')

@section('title', 'Edit Soal Bank Soal')
@section('page-title', 'Edit Soal Bank Soal')

@push('styles')
<!-- Summernote CSS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<style>
    .note-editor.note-frame {
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem;
    }
    .note-editor.note-frame.note-focus {
        border: 1px solid #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25) !important;
    }
    .note-toolbar {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
    .note-toolbar.btn-group {
        display: flex;
        flex-wrap: wrap;
    }
    .note-toolbar .btn {
        border-radius: 0.25rem;
    }
</style>
@endpush

@section('content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0">Edit Soal Bank Soal</h5>
            <small class="text-muted">Edit soal yang ada di bank soal</small>
        </div>
        <div>
            <button class="btn btn-danger btn-sm me-2" onclick="confirmDelete()">
                <i class="fas fa-trash me-1"></i> Hapus
            </button>
            <a href="{{ route('admin.bank-soal') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form id="soalForm" method="POST" action="{{ route('admin.bank-soal.update', $question->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="guru_id" class="form-label">Guru <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $question->user->nama ?? '-' }}" disabled>
                        <input type="hidden" name="guru_id" value="{{ $question->guru_id }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="course_id" class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('course_id') is-invalid @enderror"
                                id="course_id" name="course_id" required>
                            <option value="">Pilih Mata Pelajaran</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (old('course_id', $question->course_id) == $course->id) ? 'selected' : '' }}>
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

            <div class="mb-4">
                <label for="pertanyaan" class="form-label">Pertanyaan <span class="text-danger">*</span></label>
                <div id="pertanyaan" class="editor @error('pertanyaan') is-invalid @enderror">{!! old('pertanyaan', $question->pertanyaan) !!}</div>
                <input type="hidden" name="pertanyaan" id="pertanyaan_input" value="{!! old('pertanyaan', $question->pertanyaan) !!}">
                @error('pertanyaan')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jawaban_a" class="form-label">Jawaban A <span class="text-danger">*</span></label>
                        <div id="jawaban_a" class="editor @error('jawaban_a') is-invalid @enderror">{!! old('jawaban_a', $question->jawaban_a) !!}</div>
                        <input type="hidden" name="jawaban_a" id="jawaban_a_input" value="{!! old('jawaban_a', $question->jawaban_a) !!}">
                        @error('jawaban_a')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jawaban_b" class="form-label">Jawaban B <span class="text-danger">*</span></label>
                        <div id="jawaban_b" class="editor @error('jawaban_b') is-invalid @enderror">{!! old('jawaban_b', $question->jawaban_b) !!}</div>
                        <input type="hidden" name="jawaban_b" id="jawaban_b_input" value="{!! old('jawaban_b', $question->jawaban_b) !!}">
                        @error('jawaban_b')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jawaban_c" class="form-label">Jawaban C <span class="text-danger">*</span></label>
                        <div id="jawaban_c" class="editor @error('jawaban_c') is-invalid @enderror">{!! old('jawaban_c', $question->jawaban_c) !!}</div>
                        <input type="hidden" name="jawaban_c" id="jawaban_c_input" value="{!! old('jawaban_c', $question->jawaban_c) !!}">
                        @error('jawaban_c')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="jawaban_d" class="form-label">Jawaban D <span class="text-danger">*</span></label>
                        <div id="jawaban_d" class="editor @error('jawaban_d') is-invalid @enderror">{!! old('jawaban_d', $question->jawaban_d) !!}</div>
                        <input type="hidden" name="jawaban_d" id="jawaban_d_input" value="{!! old('jawaban_d', $question->jawaban_d) !!}">
                        @error('jawaban_d')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="jawaban_e" class="form-label">Jawaban E <span class="text-danger">*</span></label>
                <div id="jawaban_e" class="editor @error('jawaban_e') is-invalid @enderror">{!! old('jawaban_e', $question->jawaban_e) !!}</div>
                <input type="hidden" name="jawaban_e" id="jawaban_e_input" value="{!! old('jawaban_e', $question->jawaban_e) !!}">
                @error('jawaban_e')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label for="kunci_jawaban" class="form-label">Kunci Jawaban <span class="text-danger">*</span></label>
                <div class="border rounded p-3 bg-light">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="kunci_jawaban" id="kunci_a" value="a" {{ (old('kunci_jawaban', $question->kunci_jawaban) == 'a') ? 'checked' : '' }}>
                        <label class="form-check-label" for="kunci_a">Pilihan A</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="kunci_jawaban" id="kunci_b" value="b" {{ (old('kunci_jawaban', $question->kunci_jawaban) == 'b') ? 'checked' : '' }}>
                        <label class="form-check-label" for="kunci_b">Pilihan B</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="kunci_jawaban" id="kunci_c" value="c" {{ (old('kunci_jawaban', $question->kunci_jawaban) == 'c') ? 'checked' : '' }}>
                        <label class="form-check-label" for="kunci_c">Pilihan C</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="kunci_jawaban" id="kunci_d" value="d" {{ (old('kunci_jawaban', $question->kunci_jawaban) == 'd') ? 'checked' : '' }}>
                        <label class="form-check-label" for="kunci_d">Pilihan D</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="kunci_jawaban" id="kunci_e" value="e" {{ (old('kunci_jawaban', $question->kunci_jawaban) == 'e') ? 'checked' : '' }}>
                        <label class="form-check-label" for="kunci_e">Pilihan E</label>
                    </div>
                </div>
                @error('kunci_jawaban')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ (old('is_active', $question->is_active) ? 'checked' : '') }}>
                    <label class="form-check-label" for="is_active">
                        Soal Aktif
                    </label>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Perbarui Soal
                </button>
                <a href="{{ route('admin.bank-soal') }}" class="btn btn-secondary">
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
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Yakin ingin menghapus soal ini?</p>
                <div class="alert alert-warning">
                    <strong>Perhatian:</strong> Soal yang sudah dihapus tidak dapat dikembalikan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form method="POST" action="{{ route('admin.bank-soal.destroy', $question->id) }}" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus Soal</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- jQuery (required for Summernote) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Summernote Lite JS (Bootstrap independent version) -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<!-- Summernote Lang - Indonesian -->
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/lang/summernote-id-ID.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize all editors with full toolbar
    const editorFields = ['pertanyaan', 'jawaban_a', 'jawaban_b', 'jawaban_c', 'jawaban_d', 'jawaban_e'];

    editorFields.forEach(function(fieldId) {
        const $editor = $('#' + fieldId);
        $editor.summernote({
            placeholder: 'Masukkan teks...',
            lang: 'id-ID',
            tabsize: 2,
            height: 250,
            minHeight: 200,
            maxHeight: 500,
            toolbar: [
                ['style', ['style', 'bold', 'italic', 'underline', 'strikethrough', 'clear']],
                ['font', ['fontname', 'fontsize']],
                ['color', ['forecolor', 'backcolor']],
                ['para', ['ul', 'ol', 'paragraph', 'height']],
                ['align', ['left', 'center', 'right', 'justify']],
                ['table', ['table', 'hr']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'undo', 'redo', 'help']]
            ],
            fontNames: [
                'Segoe UI', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 
                'Georgia', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 
                'Times New Roman', 'Trebuchet MS', 'Verdana'
            ],
            fontNamesIgnoreCheck: [
                'Segoe UI', 'Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 
                'Georgia', 'Helvetica', 'Impact', 'Lucida Grande', 'Tahoma', 
                'Times New Roman', 'Trebuchet MS', 'Verdana'
            ],
            fontSizes: ['8', '9', '10', '11', '12', '14', '16', '18', '20', '24', '28', '32', '36', '48'],
            styleTags: [
                'p',
                { title: 'Blockquote', tag: 'blockquote', className: 'blockquote', value: 'blockquote' },
                'pre',
                { title: 'H1', tag: 'h1', value: 'h1' },
                { title: 'H2', tag: 'h2', value: 'h2' },
                { title: 'H3', tag: 'h3', value: 'h3' },
                { title: 'H4', tag: 'h4', value: 'h4' },
                { title: 'H5', tag: 'h5', value: 'h5' },
                { title: 'H6', tag: 'h6', value: 'h6' }
            ],
            popover: {
                image: [['imagesize', ['imagesize100', 'imagesize50', 'imagesize25']], ['float', ['floatLeft', 'floatRight', 'floatNone']], ['remove', ['removeMedia']]],
                link: [['link', ['linkEditA', 'linkRemove']]]
            },
            imageAttributes: {
                iconPrefix: '<i class="note-icon-picture"></i>',
                removeMediaBtnClassName: 'btn-outline-danger'
            },
            callbacks: {
                onImageUpload: function(files) {
                    uploadImage(files[0], $(this).attr('id'));
                },
                onChange: function(contents, $editable) {
                    // Save content to hidden input on change
                    const currentFieldId = $(this).attr('id');
                    const content = $('#' + currentFieldId).summernote('code');
                    $('#' + currentFieldId + '_input').val(content);
                },
                onInit: function() {
                    console.log('Summernote initialized');
                }
            }
        });

        // Load existing content if available
        const existingContent = $('#' + fieldId + '_input').val();
        if (existingContent) {
            $editor.summernote('code', existingContent);
        }
    });

    // Before form submission, update hidden inputs with editor content
    $('#soalForm').on('submit', function(e) {
        editorFields.forEach(function(fieldId) {
            const content = $('#' + fieldId).summernote('code');
            $('#' + fieldId + '_input').val(content);
        });
    });
});

function uploadImage(file, fieldId) {
    console.log('uploadImage called with fieldId:', fieldId);
    console.log('File name:', file.name, 'Size:', file.size);
    
    // Validate file
    const validExtensions = ['jpeg', 'jpg', 'png', 'gif', 'webp'];
    const fileExtension = file.name.split('.').pop().toLowerCase();
    
    if (!validExtensions.includes(fileExtension)) {
        alert('Format gambar tidak didukung. Gunakan: JPEG, PNG, GIF, atau WebP');
        return;
    }

    if (file.size > 5120 * 1024) { // 5MB in bytes
        alert('Ukuran gambar terlalu besar. Maksimal 5MB');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);

    $.ajax({
        url: '{{ route("admin.bank-soal.upload-image") }}',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        beforeSend: function() {
            console.log('Starting image upload to:', '{{ route("admin.bank-soal.upload-image") }}');
        },
        success: function(response) {
            console.log('Upload success, response:', response);
            
            if (response.success && response.file && response.file.url) {
                const imageUrl = response.file.url;
                console.log('Image URL:', imageUrl);
                console.log('Field ID:', fieldId);
                
                // Check if editor exists
                const $editor = $('#' + fieldId);
                console.log('Editor element found:', $editor.length > 0);
                
                if ($editor.length === 0) {
                    console.error('Editor element not found with ID:', fieldId);
                    alert('Error: Editor tidak ditemukan dengan ID: ' + fieldId);
                    return;
                }
                
                // Try using insertImage with no callback first
                try {
                    $editor.summernote('insertImage', imageUrl);
                    console.log('Image inserted using insertImage API');
                    
                    // Show success message
                    alert('Gambar berhasil diunggah!\n\nURL: ' + imageUrl);
                } catch (e) {
                    console.error('Error inserting image:', e);
                    alert('Error: ' + e.message);
                }
            } else {
                const errorMsg = response.message || 'Upload gagal tanpa pesan error';
                console.error('Upload failed:', response);
                alert('Gagal mengunggah gambar: ' + errorMsg);
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', {status, error, xhr});
            let errorMessage = 'Gagal mengunggah gambar';
            
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseText) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage = response.message || errorMessage;
                } catch (e) {
                    errorMessage = xhr.responseText.substring(0, 200);
                }
            }
            
            console.error('Error message:', errorMessage);
            alert(errorMessage);
        }
    });
}

function confirmDelete() {
    $('#deleteModal').modal('show');
}
</script>
@endpush
