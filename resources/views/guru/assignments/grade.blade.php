@extends('layouts.app')

@section('title', 'Penilaian - '.$assignment->judul)
@section('page-title', 'Penilaian - '.$assignment->judul)

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('guru.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.courses') }}">Mata Pelajaran</a></li>
        <li class="breadcrumb-item"><a href="{{ route('guru.assignments', $assignment->course_id) }}">Tugas</a></li>
        <li class="breadcrumb-item active" aria-current="page">Penilaian</li>
    </ol>
</nav>
<div class="card">
    <div class="card-body">
        <div class="mb-3">
            <a href="{{ route('guru.assignments', $assignment->course_id) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tugas
            </a>
        </div>

        <div class="mb-4">
            <h5 class="mb-1">{{ $assignment->judul }}</h5>
            <div class="text-muted">{{ $assignment->deskripsi }}</div>
        </div>

        <form method="POST" action="{{ route('guru.grades.bulk.update', $assignment->id) }}">
            @csrf
            @method('PATCH')

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama Siswa</th>
                            <th>Jawaban</th>
                            <th>File</th>
                            <th>Nilai</th>
                            <th>Feedback</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignment->grades as $grade)
                            <tr>
                                <td>{{ $grade->student->name }}</td>
                                <td class="text-muted" style="max-width: 300px">{{ \Illuminate\Support\Str::limit($grade->jawaban_text, 120) }}</td>
                                <td>
                                    @if($grade->file_jawaban)
                                        <a href="{{ asset('storage/assignments/'.$grade->file_jawaban) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-file-download me-1"></i> Unduh
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td style="width: 120px">
                                    <input type="number" name="nilai[{{ $grade->id }}]" min="0" max="100" value="{{ old('nilai.'.$grade->id, $grade->nilai) }}" class="form-control form-control-sm" placeholder="0-100">
                                </td>
                                <td>
                                    <input type="text" name="feedback[{{ $grade->id }}]" value="{{ old('feedback.'.$grade->id, $grade->feedback) }}" class="form-control form-control-sm" placeholder="Feedback">
                                </td>
                                <td>
                                    <button type="button" onclick="deleteGrade({{ $grade->id }})" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada pengumpulan tugas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-end mt-3">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Simpan Semua Nilai
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function deleteGrade(gradeId) {
    if (confirm('Apakah Anda yakin ingin menghapus nilai ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/guru/grades/${gradeId}`;
        form.style.display = 'none';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection


