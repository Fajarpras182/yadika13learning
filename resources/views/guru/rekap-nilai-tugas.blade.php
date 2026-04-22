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
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" class="d-flex">
                                <select name="assignment_type" class="form-select me-2" onchange="this.form.submit()">
                                    <option value="tugas" {{ $assignmentType == 'tugas' ? 'selected' : '' }}>Tugas</option>
                                    <option value="kuis" {{ $assignmentType == 'kuis' ? 'selected' : '' }}>Kuis</option>
                                </select>
                            </form>
                        </div>
                    </div>

                    @forelse($courses as $course)
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="card-title mb-0">{{ $course->nama_mata_pelajaran }}</h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $assignments = $assignmentType === 'kuis'
                                        ? $course->assignments->filter(function($assignment) {
                                            return str_contains(strtolower($assignment->judul), 'quiz') ||
                                                   str_contains(strtolower($assignment->judul), 'kuis');
                                        })
                                        : $course->assignments;
                                @endphp

                                @if($assignments->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Siswa</th>
                                                    <th>Tugas</th>
                                                    <th>Nilai</th>
                                                    <th>Tanggal Submit</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $no = 1; @endphp
                                                @foreach($assignments as $assignment)
                                                    @foreach($assignment->grades as $grade)
                                                        <tr>
                                                            <td>{{ $no++ }}</td>
                                                            <td>{{ $grade->student->name }}</td>
                                                            <td>{{ $assignment->judul }}</td>
                                                            <td>
                                                                @if($grade->nilai !== null)
                                                                    <span class="badge bg-{{ $grade->nilai >= 75 ? 'success' : ($grade->nilai >= 60 ? 'warning' : 'danger') }}">
                                                                        {{ $grade->nilai }}
                                                                    </span>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $grade->created_at ? $grade->created_at->format('d/m/Y H:i') : '-' }}</td>
                                                            <td>
                                                                @if($grade->status)
                                                                    <span class="badge bg-{{ $grade->status == 'sudah_dinilai' ? 'success' : 'warning' }}">
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
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Belum ada {{ $assignmentType }} untuk mata pelajaran ini</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fas fa-book fa-4x text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada mata pelajaran</h5>
                            <p class="text-muted">Anda belum mengampu mata pelajaran apapun</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
