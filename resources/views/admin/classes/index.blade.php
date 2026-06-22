@extends('layouts.app')

@section('title', 'Kelas - Admin')
@section('page-title', 'Manajemen Kelas')

@section('content')
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<form class="d-flex" method="GET" action="{{ route('admin.classes.index') }}">
			<input type="text" name="q" value="{{ request('q') }}" class="form-control me-2" placeholder="Cari nama/wali kelas">
			<button class="btn btn-primary" type="submit"><i class="fas fa-filter me-1"></i> Filter</button>
		</form>
		<div class="d-flex gap-2">
			<form method="POST" action="{{ route('admin.classes.import') }}" enctype="multipart/form-data" class="d-flex">
				@csrf
				<input type="file" name="file" class="form-control form-control-sm me-2" accept=".xlsx,.csv" required>
				<button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-file-import me-1"></i> Import</button>
			</form>
			<div class="btn-group">
				<a href="{{ route('admin.classes.export.excel') }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i></a>
				<a href="{{ route('admin.classes.export.csv') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-csv"></i></a>
				<a href="{{ route('admin.classes.export.pdf') }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a>
				<a href="{{ route('admin.classes.export.word') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-word"></i></a>
				<a href="{{ route('admin.classes.export.pdf') }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="fas fa-print"></i></a>
			</div>
			<a href="{{ route('admin.classes.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Tambah</a>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-modern">
				<thead>
					<tr>
						<th>Nama</th>
						<th>Jurusan</th>
						<th>Wali Kelas</th>
						<th>Angkatan</th>
						<th>Jumlah Siswa</th>
						<th width="140">Aksi</th>
					</tr>
				</thead>
				<tbody>
				@forelse($classes as $class)
					<tr>
						<td><strong>{{ $class->name }}</strong></td>
						<td>{{ $class->major->name ?? '-' }}</td>
						<td>{{ $class->homeroom_teacher ?? '-' }}</td>
						<td>{{ $class->year ?? '-' }}</td>
						<td>{{ $class->students_count }}</td>
						<td>
							<div class="btn-group btn-group-sm">
								<a href="{{ route('admin.classes.show', $class) }}" class="btn btn-info" title="Lihat Siswa"><i class="fas fa-eye"></i></a>
								<a href="{{ route('admin.classes.edit', $class) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
								<form method="POST" action="{{ route('admin.classes.destroy', $class) }}" onsubmit="return confirm('Hapus kelas ini?')">
									@csrf @method('DELETE')
									<button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
								</form>
							</div>
						</td>
					</tr>
				@empty
					<tr><td colspan="6" class="text-center text-muted">Belum ada data</td></tr>
				@endforelse
				</tbody>
			</table>
		</div>
		{{ $classes->links() }}
	</div>
</div>
@endsection
