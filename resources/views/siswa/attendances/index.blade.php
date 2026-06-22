@extends('layouts.app')

@section('title', 'Presensi - Siswa')

@section('page-title', 'Riwayat Presensi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Riwayat Presensi Saya
                    </h5>
                </div>
                <div class="card-body">
                    @if($attendances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern">
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendances as $attendance)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($attendance->tanggal)->format('d/m/Y') }}</td>
                                        <td>
                                            <strong>{{ $attendance->course->nama_mata_pelajaran }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $attendance->course->kode_mata_pelajaran }}</small>
                                        </td>
                                        <td>
                                            @if($attendance->status == 'hadir')
                                                <span class="badge bg-success">Hadir</span>
                                            @elseif($attendance->status == 'izin')
                                                <span class="badge bg-warning">Izin</span>
                                            @elseif($attendance->status == 'sakit')
                                                <span class="badge bg-info">Sakit</span>
                                            @else
                                                <span class="badge bg-danger">Alpha</span>
                                            @endif
                                        </td>
                                        <td>{{ $attendance->catatan ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $attendances->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada data presensi</h5>
                            <p class="text-muted">Data presensi Anda akan muncul di sini setelah guru mengisi absensi.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
