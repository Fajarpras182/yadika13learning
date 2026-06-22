@extends('layouts.app')

@section('title', 'Daftar Siswa - ' . $class->name . ' - Admin')
@section('page-title', 'Daftar Siswa Kelas ' . $class->name)

@section('content')
<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-users me-2"></i>Daftar Siswa Kelas {{ $class->name }}
        </h6>
        <a href="{{ route('admin.classes.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Jurusan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($students as $student)
                    <tr>
                        <td>{{ $student->name }}</td>
                        <td>{{ $student->nis_nip }}</td>
                        <td>{{ $class->name }}</td>
                        <td>{{ $student->jurusan }}</td>
                        <td>
                            <span class="badge {{ $student->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">Belum ada siswa di kelas ini</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $students->links() }}
    </div>
</div>
@endsection
