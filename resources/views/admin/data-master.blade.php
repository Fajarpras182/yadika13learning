@extends('layouts.app')

@section('title', 'Data Master - E-Learning SMK Yadika 13')
@section('page-title', 'Data Master')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-database me-2"></i>Data Master
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted">Kelola data master sistem E-Learning SMK Yadika 13</p>

                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-left-primary">
                            <div class="card-body text-center">
                                <i class="fas fa-layer-group fa-3x text-primary mb-3"></i>
                                <h5 class="card-title">Jurusan</h5>
                                <p class="card-text text-muted">Kelola data jurusan sekolah</p>
                                <a href="{{ route('admin.majors.index') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-right me-2"></i>Kelola Jurusan
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-left-success">
                            <div class="card-body text-center">
                                <i class="fas fa-school fa-3x text-success mb-3"></i>
                                <h5 class="card-title">Kelas</h5>
                                <p class="card-text text-muted">Kelola data kelas siswa</p>
                                <a href="{{ route('admin.classes.index') }}" class="btn btn-success">
                                    <i class="fas fa-arrow-right me-2"></i>Kelola Kelas
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-left-secondary">
                            <div class="card-body text-center">
                                <i class="fas fa-chalkboard-teacher fa-3x text-secondary mb-3"></i>
                                <h5 class="card-title">Guru</h5>
                                <p class="card-text text-muted">Kelola data guru</p>
                                <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-right me-2"></i>Kelola Guru
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-left-info">
                            <div class="card-body text-center">
                                <i class="fas fa-user-graduate fa-3x text-info mb-3"></i>
                                <h5 class="card-title">Siswa</h5>
                                <p class="card-text text-muted">Kelola data siswa</p>
                                <a href="{{ route('admin.students.index') }}" class="btn btn-info">
                                    <i class="fas fa-arrow-right me-2"></i>Kelola Siswa
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-left-dark">
                            <div class="card-body text-center">
                                <i class="fas fa-book fa-3x text-dark mb-3"></i>
                                <h5 class="card-title">Mata Pelajaran</h5>
                                <p class="card-text text-muted">Kelola mata pelajaran</p>
                                <a href="{{ route('admin.courses') }}" class="btn btn-dark">
                                    <i class="fas fa-arrow-right me-2"></i>Kelola Mata Pelajaran
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 mb-4">
                        <div class="card h-100 border-left-warning">
                            <div class="card-body text-center">
                                <i class="fas fa-calendar-alt fa-3x text-warning mb-3"></i>
                                <h5 class="card-title">Jadwal</h5>
                                <p class="card-text text-muted">Kelola jadwal pelajaran</p>
                                <a href="{{ route('admin.schedules.index') }}" class="btn btn-warning">
                                    <i class="fas fa-arrow-right me-2"></i>Kelola Jadwal
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
</style>
@endpush
