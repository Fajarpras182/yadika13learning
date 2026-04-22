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
                <div class="row mb-3">
                    <div class="col-md-6">
                        <select id="classFilter" class="form-control" onchange="filterByClass()">
                            <option value="">-- Semua Kelas --</option>
                            @php
                                $classes = \App\Models\SchoolClass::all();
                            @endphp
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 text-end">
                        <button class="btn btn-success" onclick="exportExamScores()">
                            <i class="fas fa-file-pdf me-2"></i>Export Nilai PDF
                        </button>

                    </div>
                </div>

                @if($examResults->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="examScoresTable">
                            <thead class="table-dark">
                                <tr>
                                    <th>Ujian</th>
                                    <th>Siswa</th>
                                    <th>Skor</th>
                                    <th>Persentase</th>
                                    <th>Waktu Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($examResults as $result)
                                @php
                                    $percentage = ($result->score / $result->ujian->bobot_nilai) * 100;
                                    $percentage = round($percentage, 2);
                                    if ($percentage >= 80) $scoreBadge = 'score-excellent';
                                    elseif ($percentage >= 70) $scoreBadge = 'score-good';
                                    elseif ($percentage >= 60) $scoreBadge = 'score-fair';
                                    else $scoreBadge = 'score-poor';
                                @endphp
                                <tr data-class="{{ $result->student->class_id }}">
                                    <td>
                                        <strong>{{ $result->ujian->judul ?? '-' }}</strong><br>
                                        <small class="text-muted">{{ $result->ujian->course->nama_mata_pelajaran ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $result->student->name }}</strong><br>
                                        <small class="text-muted">{{ $result->student->nis_nip }}</small>
                                    </td>
                                    <td>
                                        <span class="score-badge {{ $scoreBadge }}">
                                            {{ number_format($result->score, 2) }} / {{ $result->ujian->bobot_nilai }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $percentage }}%</strong>
                                    </td>
                                    <td>{{ $result->end_time ? $result->end_time->format('d/m/Y H:i') : '-' }}</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#reviewModal" onclick="loadReview({{ $result->id }})">
                                            <i class="fas fa-eye"></i> Review
                                        </a>
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

@endsection

@push('scripts')
<script>
function filterByClass() {
    const classId = document.getElementById('classFilter').value;
    const rows = document.querySelectorAll('#examScoresTable tbody tr');
    
    rows.forEach(row => {
        if (classId === '' || row.dataset.class === classId) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

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

function exportExamScores() {
    const classId = document.getElementById('classFilter').value;
    let url = '/guru/nilai-ujian/export';
    if (classId) {
        url += `?class_id=${classId}`;
    }
    window.location.href = url;
}
</script>
@endpush
