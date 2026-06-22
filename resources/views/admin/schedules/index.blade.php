@extends('layouts.app')

@section('title', 'Jadwal - Admin')
@section('page-title', 'Manajemen Jadwal')

@section('content')
<div class="card shadow">
	<div class="card-header d-flex justify-content-between align-items-center">
		<form class="row g-2" method="GET" action="{{ route('admin.schedules.index') }}">
			<div class="col-auto">
				<select name="course_id" class="form-select">
					<option value="">Semua Mapel</option>
					@foreach($courses as $c)
						<option value="{{ $c->id }}" {{ request('course_id')==$c->id ? 'selected' : '' }}>{{ $c->nama_mata_pelajaran }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-auto">
				<select name="class_id" class="form-select">
					<option value="">Semua Kelas</option>
					@foreach($classes as $cl)
						<option value="{{ $cl->id }}" {{ request('class_id')==$cl->id ? 'selected' : '' }}>{{ $cl->name }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-auto">
				<select name="day" class="form-select">
					<option value="">Semua Hari</option>
					@foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $d)
						<option value="{{ $d }}" {{ request('day')==$d ? 'selected' : '' }}>{{ $d }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-auto">
				<select name="status" class="form-select">
					<option value="">Semua Status</option>
					<option value="active" {{ request('status')=='active' ? 'selected' : '' }}>Aktif</option>
					<option value="inactive" {{ request('status')=='inactive' ? 'selected' : '' }}>Nonaktif</option>
				</select>
			</div>
			<div class="col-auto">
				<button class="btn btn-primary" type="submit"><i class="fas fa-filter me-1"></i> Filter</button>
			</div>
		</form>
		<div class="d-flex gap-2">
			<form method="POST" action="{{ route('admin.schedules.import') }}" enctype="multipart/form-data" class="d-flex">
				@csrf
				<input type="file" name="file" class="form-control form-control-sm me-2" accept=".xlsx,.csv" required>
				<button class="btn btn-outline-secondary btn-sm" type="submit"><i class="fas fa-file-import me-1"></i> Import</button>
			</form>
			<div class="btn-group">
				<a href="{{ route('admin.schedules.export.excel') }}" class="btn btn-outline-success btn-sm"><i class="fas fa-file-excel"></i></a>
				<a href="{{ route('admin.schedules.export.csv') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-file-csv"></i></a>
				<a href="{{ route('admin.schedules.export.pdf') }}" class="btn btn-outline-danger btn-sm"><i class="fas fa-file-pdf"></i></a>
				<a href="{{ route('admin.schedules.export.word') }}" class="btn btn-outline-primary btn-sm"><i class="fas fa-file-word"></i></a>
				<a href="{{ route('admin.schedules.export.pdf') }}" target="_blank" class="btn btn-outline-dark btn-sm"><i class="fas fa-print"></i></a>
			</div>
			<a href="{{ route('admin.schedules.create') }}" class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Tambah</a>
		</div>
	</div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-bordered align-middle">
				<thead class="table-light">
					<tr>
						<th>Hari</th>
						<th>Waktu</th>
						<th>Mata Pelajaran</th>
						<th>Kelas</th>
						<th>Ruang</th>
						<th>Status</th>
						<th width="140">Aksi</th>
					</tr>
				</thead>
				<tbody>
				@forelse($schedules as $s)
					<tr>
						<td>{{ $s->day }}</td>
						<td>{{ $s->start_time }} - {{ $s->end_time }}</td>
						<td>{{ $s->course->nama_mata_pelajaran ?? '-' }}</td>
						<td>{{ $s->schoolClass->name ?? '-' }}</td>
						<td>{{ $s->room ?? '-' }}</td>
						<td><span class="badge bg-{{ $s->is_active ? 'success' : 'secondary' }}">{{ $s->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
						<td>
							<div class="btn-group btn-group-sm">
								<a href="{{ route('admin.schedules.edit', $s) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
								<form method="POST" action="{{ route('admin.schedules.destroy', $s) }}" onsubmit="return confirm('Hapus jadwal ini?')">
									@csrf @method('DELETE')
									<button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
								</form>
							</div>
						</td>
					</tr>
				@empty
					<tr><td colspan="7" class="text-center text-muted">Belum ada data</td></tr>
				@endforelse
				</tbody>
			</table>
		</div>
		{{ $schedules->links() }}
	</div>
</div>
@endsection


