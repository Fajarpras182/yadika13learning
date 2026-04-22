@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Laporan Absensi Siswa</h3>
                </div>
                <div class="card-body">
                    <!-- Filter Form -->
                    <form method="GET" action="{{ route('guru.attendance-reports') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="course_id" class="form-label">Mata Pelajaran</label>
                                <select name="course_id" id="course_id" class="form-select">
                                    <option value="">Semua Mata Pelajaran</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->nama_mata_pelajaran }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="tanggal_from" class="form-label">Tanggal Dari</label>
                                <input type="date" name="tanggal_from" id="tanggal_from" class="form-control"
                                       value="{{ request('tanggal_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="tanggal_to" class="form-label">Tanggal Sampai</label>
                                <input type="date" name="tanggal_to" id="tanggal_to" class="form-control"
                                       value="{{ request('tanggal_to') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="hadir" {{ request('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="izin" {{ request('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="sakit" {{ request('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="alpa" {{ request('status') == 'alpa' ? 'selected' : '' }}>Alpa</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="{{ route('guru.attendance-reports') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Reset
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Export Buttons -->
                    <div class="mb-3">
                        <a href="{{ route('guru.reports.export-attendance-pdf', request()->query()) }}" class="btn btn-danger btn-sm me-2" title="Export PDF">
                            <i class="fas fa-file-pdf"></i> Export PDF
                        </a>
                    </div>

                    <!-- Attendance Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Nama Siswa</th>
                                    <th>NIS</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($attendanceData ?? [] as $index => $attendance)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $attendance['mata_pelajaran'] }}</td>
                                    <td>{{ $attendance['nama_siswa'] }}</td>
                                    <td>{{ $attendance['nis'] }}</td>
                                    <td>{{ $attendance['tanggal'] }}</td>
                                    <td>
                                        <span class="badge
                                            @if($attendance['status'] == 'hadir') bg-success
                                            @elseif($attendance['status'] == 'izin') bg-warning
                                            @elseif($attendance['status'] == 'sakit') bg-info
                                            @else bg-danger
                                            @endif">
                                            {{ ucfirst($attendance['status']) }}
                                        </span>
                                    </td>
                                    <td>{{ $attendance['keterangan'] ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Tidak ada data absensi ditemukan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(isset($attendanceData) && count($attendanceData) > 0)
                    <div class="mt-3">
                        <p class="text-muted">Total: {{ count($attendanceData) }} data absensi</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-submit form when filters change (optional)
    $('#course_id, #status').change(function() {
        // Uncomment below if you want auto-submit on filter change
        // $(this).closest('form').submit();
    });
});
</script>
@endsection
