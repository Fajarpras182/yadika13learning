@extends('layouts.app')

@section('title', 'Mata Pelajaran - Admin')
@section('page-title', 'Manajemen Mata Pelajaran')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<div class="d-flex gap-2 mb-3 mb-lg-0">
			<a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
				<i class="fas fa-plus me-2"></i>Tambah Mata Pelajaran
			</a>
		</div>
		<form class="row g-2" method="GET" action="{{ route('admin.courses') }}">
			<div class="col-auto">
				<input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode/nama/deskripsi">
			</div>
			<div class="col-auto">
				<button class="btn btn-primary" type="submit"><i class="fas fa-search me-1"></i> Cari</button>
			</div>
		</form>
		<div class="d-flex gap-2">
			<form method="POST" action="{{ route('admin.courses.import') }}" enctype="multipart/form-data" class="d-flex">
				@csrf
				<input type="file" name="file" class="form-control form-control-sm me-2" accept=".xlsx,.csv" required>
				<button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-file-import me-1"></i> Import</button>
			</form>
			<div class="btn-group">
				<a href="{{ route('admin.courses.export.excel') }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i></a>
				<a href="{{ route('admin.courses.export.csv') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-csv"></i></a>
				<a href="{{ route('admin.courses.export.pdf') }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a>
				<a href="{{ route('admin.courses.export.word') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-word"></i></a>
				<a href="{{ route('admin.courses.export.pdf') }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="fas fa-print"></i></a>
			</div>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-modern">
				<thead>
					<tr>
						<th>Kode</th>
						<th>Nama</th>
						<th>Guru</th>
						<th>Kelas</th>
						<th>Jurusan</th>
						<th>Semester</th>
						<th>Status</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
				@forelse($courses as $c)
					<tr>
						<td><strong>{{ $c->kode_mata_pelajaran }}</strong></td>
						<td>{{ $c->nama_mata_pelajaran }}</td>
						<td>{{ $c->guru->name ?? '-' }}</td>
						<td>{{ $c->schoolClass->name ?? '-' }}</td>
						<td>{{ $c->major->name ?? '-' }}</td>
						<td>{{ $c->semester }}</td>
						<td><span class="badge bg-{{ $c->is_active ? 'success' : 'secondary' }}">{{ $c->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
						<td>
							<div class="btn-group btn-group-sm">
								<a href="{{ route('admin.courses.edit', $c->id) }}" class="btn btn-primary btn-sm" title="Edit">
									<i class="fas fa-edit"></i>
								</a>
								@if($c->is_active)
									<form method="POST" action="{{ route('admin.courses.destroy', $c->id) }}" class="d-inline"
										  onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
										@csrf
										@method('DELETE')
										<button type="submit" class="btn btn-danger btn-sm" title="Hapus">
											<i class="fas fa-trash"></i>
										</button>
									</form>
								@endif
							</div>
						</td>
					</tr>
				@empty
					<tr><td colspan="8" class="text-center text-muted">Belum ada data</td></tr>
				@endforelse
				</tbody>
			</table>
		</div>
		{{ $courses->links() }}
	</div>
</div>
@endsection


