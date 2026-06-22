@extends('layouts.app')

@section('title', 'Forum Diskusi - Siswa')

@section('page-title', 'Forum Diskusi Kelas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-comments me-2"></i>Forum Diskusi Kelas
                    </h5>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newMessageModal">
                        <i class="fas fa-plus me-1"></i>Kirim Pesan
                    </button>
                </div>
                <div class="card-body">
                    @if($receivedMessages->count() > 0 || $sentMessages->count() > 0)
                        <div class="messages-container" style="max-height: 600px; overflow-y: auto;">
                            @php
                                $allMessages = collect();
                                $receivedMessages->each(function($msg) use (&$allMessages) {
                                    $allMessages->push($msg);
                                });
                                $sentMessages->each(function($msg) use (&$allMessages) {
                                    $allMessages->push($msg);
                                });
                                $allMessages = $allMessages->sortByDesc('created_at');
                            @endphp

                            @foreach($allMessages->whereNull('parent_id') as $message)
                            <div class="message-item mb-3 p-3 border rounded {{ $message->sender_id == auth()->id() ? 'bg-light' : 'bg-white' }}">
                                <div class="d-flex align-items-start">
                                    <div class="avatar me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($message->sender->name, 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong>{{ $message->sender->name }}</strong>
                                                @if($message->receiver)
                                                    <span class="badge bg-secondary ms-2">{{ $message->receiver->name }}</span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $message->created_at->diffForHumans() }}</small>
                                                @if($message->sender_id != auth()->id() && !$message->is_read)
                                                    <span class="badge bg-warning ms-2">Belum dibaca</span>
                                                @endif
                                            </div>
                                            <div class="d-flex align-items-center">
                                                @if($message->sender_id != auth()->id() && !$message->is_read)
                                                    <form action="{{ route('siswa.messages.read', $message->id) }}" method="POST" class="d-inline me-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            <i class="fas fa-check me-1"></i>Tandai Dibaca
                                                        </button>
                                                    </form>
                                                @endif
                                                @if($message->sender_id == auth()->id())
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                        <i class="fas fa-ellipsis-v"></i>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="{{ route('siswa.messages.edit', $message->id) }}">Edit</a></li>
                                                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteMessage({{ $message->id }})">Hapus</a></li>
                                                    </ul>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="message-content mt-2">
                                            <strong>{{ $message->subject }}</strong><br>
                                            {{ $message->message }}
                                        </div>
                                        @if($message->file_path)
                                        <div class="mt-2">
                                            <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-paperclip me-1"></i>Lihat File
                                            </a>
                                        </div>
                                        @endif

                                        <!-- Replies -->
                                        @if($message->replies->count() > 0)
                                        <div class="replies mt-3 ms-4">
                                            @foreach($message->replies as $reply)
                                            <div class="reply-item mb-2 p-2 border-start border-primary bg-light">
                                                <div class="d-flex align-items-start">
                                                    <div class="avatar me-2">
                                                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; font-size: 12px;">
                                                            {{ strtoupper(substr($reply->sender->name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <strong class="small">{{ $reply->sender->name }}</strong>
                                                                <small class="text-muted ms-2">{{ $reply->created_at->diffForHumans() }}</small>
                                                            </div>
                                                            @if($reply->sender_id == auth()->id())
                                                            <div class="dropdown">
                                                                <button class="btn btn-xs btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                                                    <i class="fas fa-ellipsis-v"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    <li><a class="dropdown-item" href="{{ route('siswa.messages.edit', $reply->id) }}">Edit</a></li>
                                                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteMessage({{ $reply->id }})">Hapus</a></li>
                                                                </ul>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="message-content mt-1 small">
                                                            {{ $reply->message }}
                                                        </div>
                                                        @if($reply->file_path)
                                                        <div class="mt-1">
                                                            <a href="{{ asset('storage/' . $reply->file_path) }}" target="_blank" class="btn btn-xs btn-outline-primary">
                                                                <i class="fas fa-paperclip me-1"></i>Lihat File
                                                            </a>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif

                                        <!-- Reply Button -->
                                        @if($message->sender_id != auth()->id())
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-outline-primary" onclick="replyToMessage({{ $message->id }}, '{{ $message->sender->name }}')">
                                                <i class="fas fa-reply me-1"></i>Balas
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $receivedMessages->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada pesan di forum</h5>
                            <p class="text-muted">Mulai diskusi dengan mengirim pesan pertama.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Message Modal -->
<div class="modal fade" id="newMessageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kirim Pesan Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('siswa.messages.send') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="receiver_id" class="form-label">Penerima</label>
                        <select class="form-select" id="receiver_id" name="receiver_id" required>
                            <option value="">Pilih penerima...</option>
                            @foreach(\App\Models\User::where('role', 'guru')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} (Guru)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subjek</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Pesan</label>
                        <textarea class="form-control" id="message" name="message" rows="4" required placeholder="Tulis pesan Anda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reply Message Modal -->
<div class="modal fade" id="replyMessageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Balas Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('siswa.messages.send') }}" method="POST">
                @csrf
                <input type="hidden" id="parent_id" name="parent_id" value="">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="receiver_id_reply" class="form-label">Penerima</label>
                        <select class="form-select" id="receiver_id_reply" name="receiver_id" required>
                            <option value="">Pilih penerima...</option>
                            @foreach(\App\Models\User::where('role', 'guru')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} (Guru)</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="subject_reply" class="form-label">Subjek</label>
                        <input type="text" class="form-control" id="subject_reply" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message_reply" class="form-label">Pesan</label>
                        <textarea class="form-control" id="message_reply" name="message" rows="4" required placeholder="Tulis balasan Anda..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function deleteMessage(messageId) {
    if (confirm('Apakah Anda yakin ingin menghapus pesan ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/siswa/messages/${messageId}`;

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';

        form.appendChild(csrfToken);
        form.appendChild(methodField);
        document.body.appendChild(form);
        form.submit();
    }
}

function replyToMessage(messageId, senderName) {
    document.getElementById('parent_id').value = messageId;
    document.getElementById('subject_reply').value = 'Re: ';
    document.getElementById('message_reply').value = '';
    document.getElementById('replyMessageModal').querySelector('.modal-title').textContent = 'Balas Pesan dari ' + senderName;
    new bootstrap.Modal(document.getElementById('replyMessageModal')).show();
}
</script>
@endpush
@endsection
