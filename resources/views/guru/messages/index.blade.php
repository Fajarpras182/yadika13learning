@extends('layouts.app')

@section('title', 'Forum & Pesan - E-Learning SMK Yadika 13')
@section('page-title', 'Forum & Pesan')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header py-3 bg-primary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-comments me-2"></i>Forum Pesan
                </h6>
            </div>
            <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                @forelse($messages ?? [] as $message)
                <div class="d-flex gap-3 mb-4 pb-3 border-bottom">
                    <div class="flex-shrink-0">
                        <div class="avatar rounded-circle bg-primary text-white" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                            {{ strtoupper(substr($message->user->name ?? 'U', 0, 1)) }}
                        </div>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">{{ $message->user->name ?? 'Unknown' }}</h6>
                                <small class="text-muted">{{ $message->created_at ? $message->created_at->format('d M Y H:i') : '-' }}</small>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if($message->user_id === auth()->id())
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.messages.edit', $message->id) }}">
                                            <i class="fas fa-edit me-2"></i>Edit
                                        </a>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('guru.messages.destroy', $message->id) }}" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Hapus pesan ini?')">
                                                <i class="fas fa-trash me-2"></i>Hapus
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                        <p class="mb-0 text-dark">{{ $message->message }}</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
                    <p class="text-muted">Belum ada pesan. Mulai percakapan sekarang!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow">
            <div class="card-header py-3 bg-secondary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Pesan
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('guru.messages.send') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="Tulis pesan Anda di sini..." required></textarea>
                        @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-send me-2"></i>Kirim Pesan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
