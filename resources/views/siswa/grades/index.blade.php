@extends('layouts.app')

@section('title', 'Nilai Saya - Siswa')
@section('page-title', 'Nilai Saya')

@section('content')
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('siswa.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Nilai</li>
    </ol>
</nav>
<div class="card shadow">
    <div class="card-header py-3">
        <h6 class="m-0">Daftar Nilai</h6>
    </div>
    <div class="card-body">
        @if(isset($grades) && $grades->count())
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Mata Pelajaran</th>
                            <th>Tugas</th>
                            <th>Tanggal</th>
                            <th>Nilai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grades as $grade)
                        <tr>
                            <td>{{ $grade->assignment->course->nama_mata_pelajaran ?? '-' }}</td>
                            <td>{{ $grade->assignment->judul ?? 'Tugas' }}</td>
                            <td>{{ optional($grade->submitted_at)->format('d M Y H:i') }}</td>
                            <td>{{ $grade->nilai ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $grades->links() }}
        @else
            <div class="text-muted text-center">Belum ada nilai.</div>
        @endif
    </div>
</div>
@endsection


