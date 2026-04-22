@extends('layouts.admin')

@section('title', 'Kelola Background - Admin E-Learning SMK Yadika 13')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-images me-2"></i>Kelola Background Login
                    </h6>
                    <a href="{{ route('admin.backgrounds.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-2"></i>Tambah Background
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered" id="backgroundsTable">
                            <thead class="table-dark">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Nama</th>
                                    <th width="20%">Tipe</th>
                                    <th width="20%">Preview</th>
                                    <th width="10%">Status</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($backgrounds as $index => $background)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $background->name }}</td>
                                        <td>
                                            @if($background->background_type === 'image')
                                                <span class="badge bg-info">Gambar</span>
                                            @else
                                                <span class="badge bg-secondary">Warna</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($background->background_type === 'image' && $background->background_image)
                                                <img src="{{ $background->background_url }}" alt="{{ $background->name }}" class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                                            @elseif($background->background_type === 'color')
                                                <div style="width: 60px; height: 40px; background-color: {{ $background->background_color }}; border: 1px solid #ddd; border-radius: 4px;"></div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($background->is_active)
                                                <span class="badge bg-success">Aktif</span>
                                            @else
                                                <span class="badge bg-secondary">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.backgrounds.show', $background) }}" class="btn btn-info btn-sm" title="Lihat">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.backgrounds.edit', $background) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.backgrounds.toggle', $background) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $background->is_active ? 'btn-secondary' : 'btn-success' }}" title="{{ $background->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <i class="fas fa-{{ $background->is_active ? 'times' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.backgrounds.destroy', $background) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus background ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <i class="fas fa-images fa-2x mb-2"></i>
                                            <br>Belum ada background yang ditambahkan.
                                            <br><a href="{{ route('admin.backgrounds.create') }}" class="btn btn-primary btn-sm mt-2">Tambah Background Pertama</a>
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
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#backgroundsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
        },
        "order": [[ 0, "asc" ]],
        "columnDefs": [
            { "orderable": false, "targets": [3, 5] }
        ]
    });
});
</script>
@endpush
