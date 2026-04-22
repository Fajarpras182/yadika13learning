@extends('layouts.app')

@section('title', 'Laporan Kehadiran - E-Learning SMK Yadika 13')
@section('page-title', 'Laporan Kehadiran')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-users me-2"></i>Laporan Kehadiran Siswa
                </h6>
            </div>
            <div class="card-body">
                <!-- Filter Form -->
                <form method="GET" action="{{ route('guru.attendance-reports') }}" class="mb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="course_id" class="form-label">Mata Pelajaran</label>
                            <select name="course_id" id="course_id" class="form-select">
                                <option value="">-- Semua Mata Pelajaran --</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                        {{ $course->nama_mata_pelajaran }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="tanggal_from" class="form-label">Dari Tanggal</label>
                            <input type="date" name="tanggal_from" id="tanggal_from" class="form-control"
                                   value="{{ request('tanggal_from') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="tanggal_to" class="form-label">Sampai Tanggal</label>
                            <input type="date" name="tanggal_to" id="tanggal_to" class="form-control"
                                   value="{{ request('tanggal_to') }}">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">-- Semua Status --</option>
                                <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                <option value="alpa" {{ request('status') == 'alpa' ? 'selected' : '' }}>Alpa</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('guru.attendance-reports') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Export Button -->
                <div class="mb-3 border-top pt-3">
                    <a href="{{ route('guru.reports.export-attendance-pdf', request()->query()) }}" class="btn btn-danger btn-sm" title="Export PDF">
                        <i class="fas fa-file-pdf me-1"></i> Export PDF
                    </a>
                </div>

                <!-- Attendance Table -->
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;">No</th>
                                <th>Mata Pelajaran</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($attendances ?? [] as $index => $attendance)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $attendance->course->nama_mata_pelajaran ?? '-' }}</td>
                                <td>{{ $attendance->student->name ?? '-' }}</td>
                                <td>{{ $attendance->student->nis_nip ?? '-' }}</td>
                                <td>{{ $attendance->tanggal ? \Carbon\Carbon::parse($attendance->tanggal)->format('d/m/Y') : '-' }}</td>
                                <td>
                                    @php
                                        $status_colors = [
                                            'hadir' => 'success',
                                            'izin' => 'info',
                                            'sakit' => 'warning',
                                            'alpa' => 'danger'
                                        ];
                                        $status_label = [
                                            'hadir' => 'Hadir',
                                            'izin' => 'Izin',
                                            'sakit' => 'Sakit',
                                            'alpa' => 'Alpa'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $status_colors[$attendance->status] ?? 'secondary' }}">
                                        {{ $status_label[$attendance->status] ?? $attendance->status }}
                                    </span>
                                </td>
                                <td>{{ $attendance->keterangan ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fas fa-inbox fa-2x mb-2" style="opacity: 0.5;"></i>
                                    <p>Tidak ada data kehadiran yang ditemukan</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
