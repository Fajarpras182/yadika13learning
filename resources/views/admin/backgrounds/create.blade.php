@extends('layouts.admin')

@section('title', 'Tambah Background - Admin E-Learning SMK Yadika 13')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus me-2"></i>Tambah Background Baru
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.backgrounds.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="background_type" class="form-label">Tipe Background</label>
                                    <select class="form-select @error('background_type') is-invalid @enderror" id="background_type" name="background_type">
                                        <option value="image">Gambar</option>
                                        <option value="color">Warna Solid</option>
                                    </select>
                                    @error('background_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="image_upload" style="display: block;">
                                    <label for="background_image" class="form-label">Upload Gambar Background</label>
                                    <input type="file" class="form-control @error('background_image') is-invalid @enderror" id="background_image" name="background_image" accept="image/*">
                                    <div class="form-text">Format yang didukung: JPG, PNG, GIF. Ukuran maksimal: 2MB</div>
                                    @error('background_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3" id="color_picker" style="display: none;">
                                    <label for="background_color" class="form-label">Pilih Warna Background</label>
                                    <input type="color" class="form-control @error('background_color') is-invalid @enderror" id="background_color" name="background_color" value="#f8f9fa">
                                    @error('background_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                        <label class="form-check-label" for="is_active">
                                            Aktifkan background ini
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="m-0">Preview</h6>
                                    </div>
                                    <div class="card-body">
                                        <div id="preview_container" style="height: 200px; background-color: #f8f9fa; border: 1px solid #ddd; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                            <span class="text-muted">Preview akan muncul di sini</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('admin.backgrounds.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Kembali
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Simpan Background
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('background_type').addEventListener('change', function() {
    const type = this.value;
    const imageUpload = document.getElementById('image_upload');
    const colorPicker = document.getElementById('color_picker');
    const preview = document.getElementById('preview_container');

    if (type === 'image') {
        imageUpload.style.display = 'block';
        colorPicker.style.display = 'none';
        preview.innerHTML = '<span class="text-muted">Upload gambar untuk melihat preview</span>';
    } else {
        imageUpload.style.display = 'none';
        colorPicker.style.display = 'block';
        updateColorPreview();
    }
});

document.getElementById('background_color').addEventListener('input', updateColorPreview);

function updateColorPreview() {
    const color = document.getElementById('background_color').value;
    const preview = document.getElementById('preview_container');
    preview.style.backgroundColor = color;
    preview.innerHTML = '';
}

document.getElementById('background_image').addEventListener('change', function() {
    const file = this.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('preview_container');
            preview.style.backgroundImage = `url(${e.target.result})`;
            preview.style.backgroundSize = 'cover';
            preview.style.backgroundPosition = 'center';
            preview.innerHTML = '';
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endpush
