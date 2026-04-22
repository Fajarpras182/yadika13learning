@extends('layouts.app')

@section('title', 'Jadwal Pelajaran - Siswa')

@section('page-title', 'Jadwal Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Jadwal Pelajaran Saya
                    </h5>
                </div>
                <div class="card-body">
                    @if($schedules->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Hari</th>
                                        <th>Jam</th>
                                        <th>Mata Pelajaran</th>
                                        <th>Guru</th>
                                        <th>Ruangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($schedules as $schedule)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ ucfirst($schedule->hari) }}
                                            </span>
                                        </td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($schedule->jam_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($schedule->jam_selesai)->format('H:i') }}
                                        </td>
                                        <td>
                                            <strong>{{ $schedule->course->nama }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $schedule->course->kode }}</small>
                                        </td>
                                        <td>{{ $schedule->guru->name }}</td>
                                        <td>{{ $schedule->ruangan ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada jadwal pelajaran</h5>
                            <p class="text-muted">Jadwal pelajaran Anda akan muncul di sini setelah ditambahkan oleh admin.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
