@extends('layouts.app')

@section('title', 'Laporan - E-Learning SMK Yadika 13')
@section('page-title', 'Laporan')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-file-alt me-2"></i>Laporan Pembelajaran
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <a href="{{ route('guru.attendance-reports') }}" class="card text-center text-decoration-none report-card">
                            <div class="card-body">
                                <i class="fas fa-users fa-3x mb-3" style="color: #667eea;"></i>
                                <h5 class="card-title">Laporan Kehadiran</h5>
                                <p class="card-text text-muted small">Lihat dan download laporan kehadiran siswa</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-4">
                        <a href="{{ route('guru.rekap-nilai-tugas') }}" class="card text-center text-decoration-none report-card">
                            <div class="card-body">
                                <i class="fas fa-tasks fa-3x mb-3" style="color: #28a745;"></i>
                                <h5 class="card-title">Laporan Nilai Tugas</h5>
                                <p class="card-text text-muted small">Lihat dan download laporan nilai tugas</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 mb-4">
                        <a href="{{ route('guru.nilai-ujian') }}" class="card text-center text-decoration-none report-card">
                            <div class="card-body">
                                <i class="fas fa-graduation-cap fa-3x mb-3" style="color: #ff6b6b;"></i>
                                <h5 class="card-title">Laporan Nilai Ujian</h5>
                                <p class="card-text text-muted small">Lihat dan download laporan nilai ujian</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header py-3 bg-secondary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-book me-2"></i>Mata Pelajaran Anda
                </h6>
            </div>
            <div class="card-body">
                @if($courses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Kelas</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <strong>{{ $course->nama_mata_pelajaran }}</strong><br>
                                        <small class="text-muted">Kode: {{ $course->kode_mata_pelajaran }}</small>
                                    </td>
                                    <td>
                                        @if($course->schoolClass)
                                            {{ $course->schoolClass->nama_kelas }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('guru.courses.edit', $course->id) }}" class="btn btn-sm btn-info" title="Edit Mata Pelajaran">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Informasi:</strong> Anda belum memiliki mata pelajaran. Silakan hubungi administrator.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .report-card {
        border: 2px solid #e8e8e8;
        transition: all 0.3s ease;
        border-radius: 8px;
    }
    
    .report-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
        transform: translateY(-5px);
    }
    
    .report-card .card-body {
        padding: 30px 20px;
    }
</style>
@endsection
