@forelse($messages ?? [] as $message)
<div class="message-group mb-4">
    <div class="d-flex gap-3 pb-3 border-bottom">
        <div class="flex-shrink-0">
            <div class="avatar rounded-circle bg-primary text-white" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                {{ strtoupper(substr($message->user->name ?? 'U', 0, 1)) }}
            </div>
        </div>
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="mb-1">
                        <strong>{{ $message->user->name ?? 'Unknown' }}</strong>
                        @if($message->user->role === 'guru')
                            <span class="badge bg-danger ms-1" style="font-size: 0.6rem;">GURU</span>
                        @endif
                    </h6>
                    <small class="text-muted">{{ $message->created_at ? $message->created_at->diffForHumans() : '-' }}</small>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-link" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="javascript:void(0)" onclick="replyMessage({{ $message->id }}, '{{ $message->user->name }}')">
                                <i class="fas fa-reply me-2"></i>Balas
                            </a>
                        </li>
                        @if($message->sender_id === auth()->id())
                        <li>
                            <form method="POST" action="{{ route('siswa.messages.destroy', $message->id) }}" class="d-inline">
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
            <p class="mb-2 text-dark" style="white-space: pre-wrap;">{{ $message->message }}</p>
            
            <!-- Replies -->
            @if($message->replies->count() > 0)
            <div class="replies-container ms-4 mt-3 ps-3 border-start">
                @foreach($message->replies as $reply)
                <div class="reply-item mb-3 pb-2 border-bottom border-light">
                    <div class="d-flex justify-content-between align-items-start">
                        <h6 class="mb-1" style="font-size: 0.9rem;">
                            <strong>{{ $reply->user->name ?? 'Unknown' }}</strong>
                            @if($reply->user->role === 'guru')
                                <span class="badge bg-danger ms-1" style="font-size: 0.5rem;">GURU</span>
                            @endif
                        </h6>
                        <small class="text-muted" style="font-size: 0.75rem;">{{ $reply->created_at->diffForHumans() }}</small>
                    </div>
                    <p class="mb-0 text-muted" style="font-size: 0.9rem; white-space: pre-wrap;">{{ $reply->message }}</p>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@empty
<div class="text-center py-5">
    <i class="fas fa-inbox fa-3x text-muted mb-3" style="opacity: 0.5;"></i>
    <p class="text-muted">Belum ada pesan. Mulai percakapan sekarang!</p>
</div>
@endforelse
