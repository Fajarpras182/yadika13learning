@extends('layouts.app')

@section('title', 'Nilai Ujian')
@section('page-title', 'Nilai Ujian')

@section('content')
<style>
    .score-badge {
        padding: 8px 12px;
        border-radius: 4px;
        font-weight: bold;
        display: inline-block;
    }
    .score-excellent { background: #d4edda; color: #155724; }
    .score-good { background: #cfe2ff; color: #084298; }
    .score-fair { background: #fff3cd; color: #664d03; }
    .score-poor { background: #f8d7da; color: #842029; }
</style>

<div class="row mb-3">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chart-bar me-2"></i>Nilai Ujian Siswa
                </h6>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <div class="card bg-light mb-4 border-0">
                    <div class="card-body">
                        <form method="GET" action="{{ route('guru.nilai-ujian') }}" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Pilih Kelas</label>
                                    <select name="class_id" class="form-select">
                                        <option value="">-- Semua Kelas --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Nama Siswa (Search)</label>
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" placeholder="Cari nama siswa..." value="{{ request('search') }}">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-bold">Tipe Ujian</label>
                                    <select name="type" class="form-select">
                                        <option value="">-- Semua Tipe --</option>
                                        <option value="UTS" {{ request('type') == 'UTS' ? 'selected' : '' }}>UTS</option>
                                        <option value="UAS" {{ request('type') == 'UAS' ? 'selected' : '' }}>UAS</option>
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end gap-2">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-1"></i> Search
                                    </button>
                                    <a href="{{ route('guru.nilai-ujian') }}" class="btn btn-secondary w-100">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mb-3 text-end">
                    <button class="btn btn-success" onclick="exportExamScores()">
                        <i class="fas fa-file-pdf me-2"></i>Export Nilai PDF
                    </button>
                </div>

                @if($examResults->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="examScoresTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ujian</th>
                                    <th>Siswa</th>
                                    <th class="text-center">Skor</th>
                                    <th class="text-center">Persentase</th>
                                    <th>Waktu Selesai</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examResults as $result)
                                @php
                                    $percentage = ($result->score / ($result->ujian->bobot_nilai ?: 100)) * 100;
                                    $percentage = round($percentage, 2);
                                    if ($percentage >= 80) $scoreBadge = 'score-excellent';
                                    elseif ($percentage >= 70) $scoreBadge = 'score-good';
                                    elseif ($percentage >= 60) $scoreBadge = 'score-fair';
                                    else $scoreBadge = 'score-poor';
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $result->ujian->judul ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $result->ujian->course->nama_mata_pelajaran ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $result->student->name }}</strong><br>
                                        <small class="text-muted">{{ $result->student->nis_nip }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="score-badge {{ $scoreBadge }}">
                                            {{ number_format($result->score, 2) }} / {{ $result->ujian->bobot_nilai }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <strong>{{ $percentage }}%</strong>
                                    </td>
                                    <td>{{ $result->end_time ? $result->end_time->format('d/m/Y H:i') : '-' }}</td>
                                    <td class="text-center d-flex justify-content-center gap-1">
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#reviewModal" onclick="loadReview({{ $result->id }})">
                                            <i class="fas fa-eye"></i> Review
                                        </a>
                                        <button class="btn btn-sm btn-warning" onclick="editScore({{ $result->id }}, {{ $result->score }}, '{{ $result->student->name }}')">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-chart-bar fa-3x mb-3"></i>
                        <h5>Belum ada data nilai ujian</h5>
                        <p class="text-muted">Data akan muncul setelah siswa menyelesaikan ujian.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Review Jawaban Ujian</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reviewContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-3">Memuat data...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Score Modal -->
<div class="modal fade" id="editScoreModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editScoreForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Skor Ujian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Siswa</label>
                        <input type="text" id="editStudentName" class="form-control bg-light" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Skor Baru</label>
                        <input type="number" name="score" id="editScoreInput" class="form-control" step="0.01" min="0" required>
                        <small class="text-muted">Masukkan angka skor (bisa desimal, misal: 85.5)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function loadReview(resultId) {
    const reviewContent = document.getElementById('reviewContent');
    reviewContent.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-3">Memuat data...</p></div>';
    
    fetch(`/guru/nilai-ujian/${resultId}/review`)
        .then(response => response.text())
        .then(html => {
            reviewContent.innerHTML = html;
        })
        .catch(error => {
            reviewContent.innerHTML = '<div class="alert alert-danger">Gagal memuat data</div>';
            console.error('Error:', error);
        });
}

function editScore(id, score, name) {
    const modal = new bootstrap.Modal(document.getElementById('editScoreModal'));
    const form = document.getElementById('editScoreForm');
    form.action = `/guru/nilai-ujian/${id}/update-score`;
    document.getElementById('editScoreInput').value = score;
    document.getElementById('editStudentName').value = name;
    modal.show();
}

function exportExamScores() {
    const params = new URLSearchParams(window.location.search);
    let url = '{{ route('guru.nilai-ujian.export') }}?' + params.toString();
    window.location.href = url;
}
</script>
@endpush
