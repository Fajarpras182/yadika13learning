@extends('layouts.app')

@section('title', 'Rekap Nilai Tugas')

@section('page-title', 'Rekap Nilai Tugas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Rekap Nilai Tugas</h5>
                        <div class="btn-group" role="group">
                            <a href="{{ route('guru.reports.export-pdf') }}" class="btn btn-danger btn-sm">
                                <i class="fas fa-file-pdf"></i> Export PDF
                            </a>
                            <a href="{{ route('guru.reports.export-excel') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel"></i> Export Excel
                            </a>
                            <a href="{{ route('guru.reports.export-word') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-file-word"></i> Export Word
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Filter -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <form method="GET" action="{{ route('guru.rekap-nilai-tugas') }}" id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Pilih Kelas</label>
                                        <select name="class_id" id="classSelect" class="form-select">
                                            <option value="">-- Semua Kelas --</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Nama Siswa (Search)</label>
                                        <div class="input-group">
                                            <input type="text" name="search" class="form-control" placeholder="Cari nama..." value="{{ $search }}">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold">Tipe</label>
                                        <select name="assignment_type" class="form-select">
                                            <option value="tugas" {{ $assignmentType == 'tugas' ? 'selected' : '' }}>Tugas</option>
                                            <option value="kuis" {{ $assignmentType == 'kuis' ? 'selected' : '' }}>Kuis</option>
                                        </select>
                                    </div>
                                    <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                        <a href="{{ route('guru.rekap-nilai-tugas') }}" class="btn btn-secondary px-4">
                                            <i class="fas fa-undo me-1"></i> Reset
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    @php $hasData = false; @endphp
                    @foreach($courses as $course)
                        @php
                            $assignments = $assignmentType === 'kuis'
                                ? $course->assignments->filter(function($assignment) {
                                    return str_contains(strtolower($assignment->judul), 'quiz') ||
                                           str_contains(strtolower($assignment->judul), 'kuis');
                                })
                                : $course->assignments;
                            
                            // Filter assignments that have at least one grade (after controller filtering)
                            $assignmentsWithGrades = $assignments->filter(function($assignment) {
                                return $assignment->grades->count() > 0;
                            });
                        @endphp

                        @if($assignmentsWithGrades->count() > 0)
                            @php $hasData = true; @endphp
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom py-3">
                                    <h6 class="card-title mb-0 text-primary fw-bold">
                                        <i class="fas fa-book me-2"></i> {{ $course->nama_mata_pelajaran }} 
                                        <span class="badge bg-light text-dark ms-2 fw-normal">{{ $course->schoolClass->name ?? '' }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">No</th>
                                                    <th>Nama Siswa</th>
                                                    <th>Tugas</th>
                                                    <th class="text-center">Nilai</th>
                                                    <th>Tanggal Submit</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = 1; @endphp
                                                @foreach($assignmentsWithGrades as $assignment)
                                                    @foreach($assignment->grades as $grade)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div class="avatar-sm me-2 bg-light rounded-circle text-center" style="width: 30px; height: 30px; line-height: 30px;">
                                                                        {{ strtoupper(substr($grade->student->name, 0, 1)) }}
                                                                    </div>
                                                                    <span>{{ $grade->student->name }}</span>
                                                                </div>
                                                            </td>
                                                            <td>{{ $assignment->judul }}</td>
                                                            <td class="text-center">
                                                                @if($grade->nilai !== null)
                                                                    <span class="badge rounded-pill bg-{{ $grade->nilai >= 75 ? 'success' : ($grade->nilai >= 60 ? 'warning' : 'danger') }}" style="min-width: 45px;">
                                                                        {{ $grade->nilai }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted small">Belum dinilai</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $grade->created_at ? $grade->created_at->format('d/m/Y H:i') : '-' }}</td>
                                                            <td class="text-center">
                                                                @if($grade->status)
                                                                    @php
                                                                        $badgeClass = 'bg-warning';
                                                                        if($grade->status == 'sudah_dinilai' || $grade->status == 'graded') $badgeClass = 'bg-success';
                                                                        if($grade->status == 'pending') $badgeClass = 'bg-info';
                                                                    @endphp
                                                                    <span class="badge {{ $badgeClass }}">
                                                                        {{ ucfirst(str_replace('_', ' ', $grade->status)) }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if(!$hasData)
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="fas fa-search fa-4x text-muted opacity-25"></i>
                            </div>
                            <h5 class="text-muted">Data tidak ditemukan</h5>
                            <p class="text-muted">Coba sesuaikan filter pencarian Anda</p>
                            <a href="{{ route('guru.rekap-nilai-tugas') }}" class="btn btn-outline-secondary btn-sm">
                                Lihat Semua Data
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
