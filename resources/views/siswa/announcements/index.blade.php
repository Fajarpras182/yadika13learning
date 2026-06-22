@extends('layouts.app')

@section('title', 'Pengumuman - Siswa')

@section('page-title', 'Pengumuman')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bullhorn me-2"></i>Pengumuman Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    @if($announcements->count() > 0)
                        <div class="announcements-container">
                            @foreach($announcements as $announcement)
                            <div class="announcement-item mb-4 p-4 border rounded bg-light">
                                <div class="d-flex align-items-start">
                                    <div class="announcement-icon me-3">
                                        <i class="fas fa-bullhorn fa-2x text-warning"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-1">{{ $announcement->sender->name }}</h6>
                                            <small class="text-muted">{{ $announcement->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($announcement->course)
                                            <span class="badge bg-info mb-2">{{ $announcement->course->nama }}</span>
                                        @endif
                                        <div class="announcement-content">
                                            {{ $announcement->content }}
                                        </div>
                                        @if($announcement->file_path)
                                        <div class="mt-3">
                                            <a href="{{ asset('storage/' . $announcement->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-paperclip me-1"></i>Lihat Lampiran
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $announcements->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada pengumuman</h5>
                            <p class="text-muted">Pengumuman dari guru akan muncul di sini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
