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
            <div class="card-body" id="message-list-container" style="max-height: 600px; overflow-y: auto;">
                @include('guru.messages._list')
            </div>
            <div class="card-footer bg-white border-top-0 pb-3">
                {{ $messages->links() }}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow border-0">
            <div class="card-header py-3 bg-primary text-white border-0">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Pesan Baru
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('guru.messages.send') }}" id="mainMessageForm">
                    @csrf
                    <div class="mb-3">
                        <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" placeholder="Tulis pesan atau topik diskusi baru di sini..." required></textarea>
                        @error('message')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="fas fa-paper-plane me-2"></i>Kirim ke Forum
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form method="POST" action="{{ route('guru.messages.send') }}">
                @csrf
                <input type="hidden" name="parent_id" id="reply_parent_id">
                <div class="modal-header bg-light">
                    <h5 class="modal-title">Balas Pesan: <span id="reply_to_name" class="text-primary"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <textarea class="form-control" name="message" rows="4" placeholder="Tulis balasan Anda..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-reply me-1"></i> Balas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function replyMessage(id, name) {
        document.getElementById('reply_parent_id').value = id;
        document.getElementById('reply_to_name').innerText = name;
        new bootstrap.Modal(document.getElementById('replyModal')).show();
    }

    // Polling for real-time updates
    function refreshMessages() {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const container = document.getElementById('message-list-container');
            // Store scroll position
            const scrollPos = container.scrollTop;
            container.innerHTML = html;
            // Restore scroll position
            container.scrollTop = scrollPos;
        })
        .catch(error => console.error('Error refreshing messages:', error));
    }

    // Refresh every 10 seconds
    setInterval(refreshMessages, 10000);
</script>
@endpush
@endsection
