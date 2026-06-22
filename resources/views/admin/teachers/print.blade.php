@extends('layouts.app')

@section('title', 'Cetak Daftar Guru - E-Learning SMK Yadika 13')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Daftar Guru</h4>
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
                                <th>NIP</th>
                                <th>No. HP</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($teachers as $index => $teacher)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>
                                    <strong>{{ $teacher->name }}</strong>
                                    @if($teacher->alamat)
                                        <br><small>{{ $teacher->alamat }}</small>
                                    @endif
                                </td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ $teacher->nis_nip }}</td>
                                <td>{{ $teacher->no_hp ?: '-' }}</td>
                                <td>
                                    @if($teacher->is_active)
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
