@extends('layouts.app')

@section('title', 'Cetak Daftar Siswa - E-Learning SMK Yadika 13')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Daftar Siswa</h4>
                <small class="text-muted">E-Learning SMK Yadika 13</small>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-sm">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Jurusan</th>
                                <th>No. HP</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $student->name }}</strong>
                                    @if($student->alamat)
                                        <br><small>{{ $student->alamat }}</small>
                                    @endif
                                </td>
                                <td>{{ $student->email }}</td>
                                <td>{{ $student->nis_nip }}</td>
                                <td>{{ $student->kelas ?: '-' }}</td>
                                <td>{{ $student->jurusan ?: '-' }}</td>
                                <td>{{ $student->no_hp ?: '-' }}</td>
                                <td>
                                    @if($student->is_active)
                                        <span class="badge bg-success">Aktif</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() {
        window.print();
    }
</script>
@endsection

@push('styles')
<style>
@media print {
    .card-header {
        background-color: #f8f9fc !important;
        -webkit-print-color-adjust: exact;
    }
    .table-light {
        background-color: #f8f9fc !important;
        -webkit-print-color-adjust: exact;
    }
    .badge {
        border: 1px solid #000;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@endpush
