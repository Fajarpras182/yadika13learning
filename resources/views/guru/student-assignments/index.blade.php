@extends('layouts.app')

@section('title', 'Tugas Siswa - E-Learning SMK Yadika 13')
@section('page-title', 'Tugas Siswa')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-tasks me-2"></i>Pengumpulan Tugas Siswa
                </h6>
            </div>
            <div class="card-body">
                @if($assignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Tugas</th>
                                    <th>Batas Waktu</th>
                                    <th>Pengumpulan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($assignments as $assignment)
                                <tr>
                                    <td>
                                        <strong>{{ $assignment->course->nama_mata_pelajaran ?? '-' }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ $assignment->judul }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($assignment->deskripsi, 50) }}</small>
                                    </td>
                                    <td>
                                        {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td>
                                        @php
                                            $submissionCount = $assignment->grades ? $assignment->grades->count() : 0;
                                            $totalStudents = $assignment->course->schoolClass ? $assignment->course->schoolClass->students->count() : 0;
                                        @endphp
                                        <span class="badge bg-info">{{ $submissionCount }} / {{ $totalStudents }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('guru.assignments', $assignment->course_id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center py-5">
                        <i class="fas fa-inbox fa-3x mb-3" style="opacity: 0.5;"></i>
                        <h5>Belum ada tugas</h5>
                        <p class="text-muted">Anda belum membuat tugas apapun. Mulai buat tugas baru di menu Tugas.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
