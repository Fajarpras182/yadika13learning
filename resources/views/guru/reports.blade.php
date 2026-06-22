@extends('layouts.app')

@section('title', 'Rekap Nilai - E-Learning SMK Yadika 13')
@section('page-title', 'Rekap Nilai')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-chart-line me-2"></i>Rekap Nilai Tugas
                </h6>
                <div class="export-buttons">
                    <a href="{{ route('guru.reports.export-pdf') }}" class="btn btn-danger btn-sm me-2" title="Export PDF">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('guru.reports.export-excel') }}" class="btn btn-success btn-sm me-2" title="Export Excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('guru.reports.export-word') }}" class="btn btn-primary btn-sm" title="Export Word">
                        <i class="fas fa-file-word"></i> Word
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if(count($gradeData) > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered" id="gradesTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Mata Pelajaran</th>
                                    <th>Tugas</th>
                                    <th>Nama Siswa</th>
                                    <th>Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($gradeData as $index => $grade)
                                <tr>
                                    <td>{{ $grade['course_name'] }}</td>
                                    <td>{{ $grade['assignment_title'] }}</td>
                                    <td>{{ $grade['student_name'] }}</td>
                                    <td>
                                        @if($grade['grade'] !== null)
                                            <span class="badge bg-success">{{ $grade['grade'] }}</span>
                                        @else
                                            <span class="badge bg-warning">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Belum ada data nilai</h5>
                        <p class="text-muted">Data nilai akan muncul setelah Anda memberikan penilaian pada tugas siswa.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.table th {
    background-color: #1010f7ff;
    border-top: 1px solid #e3e6f0;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('#gradesTable').DataTable({
        "pageLength": 25,
        "ordering": true,
        "searching": true,
        "paging": true,
        "language": {
            "lengthMenu": "Tampilkan _MENU_ data per halaman",
            "zeroRecords": "Tidak ada data yang ditemukan",
            "info": "Menampilkan halaman _PAGE_ dari _PAGES_",
            "infoEmpty": "Tidak ada data tersedia",
            "infoFiltered": "(difilter dari _MAX_ total data)",
            "search": "Cari:",
            "paginate": {
                "first": "Pertama",
                "last": "Terakhir",
                "next": "Selanjutnya",
                "previous": "Sebelumnya"
            }
        }
    });
});
</script>
@endpush
