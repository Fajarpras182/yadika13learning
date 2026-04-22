@extends('layouts.app')

@section('title', 'Manajemen Ujian - Admin')
@section('page-title', 'Manajemen Ujian')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>
        <i class="fas fa-clipboard-list me-2"></i>
        Ujian
    </h2>
    <div class="btn-group">
        <a href="{{ route('admin.ujian.create') }}" class="btn btn-primary" title="Buat ujian baru">
            <i class="fas fa-plus-circle me-2"></i>Tambah Ujian Baru
        </a>
        <button class="btn btn-outline-success" onclick="exportExams()">
            <i class="fas fa-download me-2"></i>Export
        </button>
    </div>
</div>

@if($exams->count() > 0)
<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>Ujian</th>
                        <th>Mata Pelajaran</th>
                        <th>Jadwal</th>
                        <th>Durasi</th>
                        <th>Status</th>
                        <th>Soal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($exams as $exam)
                    <tr>
                        <td>{{ Str::limit($exam->judul, 40) }}</td>
                        <td>{{ $exam->course->nama_mata_pelajaran ?? '-' }}</td>
                        <td>{{ $exam->tanggal_ujian->format('d/m/Y H:i') }}</td>
                        <td>{{ $exam->durasi_menit }} menit</td>
                        <td>
                            @if($exam->is_active)
                                <span class="badge bg-success">Aktif</span>
                            @else
                                <span class="badge bg-warning">Draft</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="badge bg-info fs-6">{{ $exam->questions_count ?? 0 }}</span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn btn-sm btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false" title="Menu ujian">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.ujian.show', $exam->id) }}">
                                            <i class="fas fa-info-circle me-2 text-primary"></i>Detail Ujian
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.bank-soal', ['ujian_id' => $exam->id, 'course_id' => $exam->course_id]) }}">
                                            <i class="fas fa-database me-2 text-success"></i>Kelola bank soal
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.ujian.edit', $exam->id) }}">
                                            <i class="fas fa-edit me-2 text-warning"></i>Edit ujian
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger" onclick="confirmDelete({{ $exam->id }}, '{{ addslashes($exam->judul) }}')">
                                            <i class="fas fa-trash me-2"></i>Hapus ujian
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@else
<div class="text-center py-5">
    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
    <h4 class="text-muted">Belum ada ujian dibuat</h4>
    <p class="text-muted">Buat ujian pertama Anda sekarang</p>
    <a href="{{ route('admin.ujian.create') }}" class="btn btn-primary btn-lg">
        <i class="fas fa-plus me-2"></i>Tambah Ujian
    </a>
</div>
@endif

@endsection

@push('scripts')
<script>
function confirmDelete(id, title) {
    if (confirm('Yakin ingin menghapus ujian "' + title + '"? Ujian yang sudah dihapus tidak dapat dikembalikan.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/ujian/${id}`;

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

function exportExams() {
    // TODO: Export functionality
    alert('Fitur export akan segera tersedia');
}
</script>
@endpush
