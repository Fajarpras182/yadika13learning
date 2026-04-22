@extends('layouts.app')

@section('title', 'Ujian Saya - Siswa')
@section('page-title', 'Daftar Ujian')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div>
                        <h5 class="mb-1"><i class="fas fa-file-alt me-2"></i>Daftar Ujian</h5>
                        <p class="mb-0 text-muted">Ujian yang tersedia untuk dikerjakan oleh siswa.</p>
                    </div>
                    <div class="text-md-end mt-3 mt-md-0">
                        <div class="badge bg-info text-dark mb-2">Waktu Sistem: <span id="jakartaTime">{{ $currentTime->format('d/m/Y H:i:s') }} WIB</span></div>
                        <div class="small text-muted">Timestamp: <span id="jakartaTimestamp">{{ $currentTime->timestamp }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        @php
            $activeCount = $sessions->filter(function ($session) use ($currentTime) {
                return $currentTime->between($session->waktu_mulai->timezone('Asia/Jakarta'), $session->waktu_selesai->timezone('Asia/Jakarta'));
            })->count();
        @endphp

        @if($activeCount > 0)
            <div class="alert alert-success d-flex align-items-center mb-4">
                <i class="fas fa-check-circle me-2"></i>
                <div>Ditemukan {{ $activeCount }} ujian yang sedang berlangsung. Tombol akan berubah secara otomatis sesuai waktu Jakarta.</div>
            </div>
        @elseif($sessions->count() > 0)
            <div class="alert alert-info d-flex align-items-center mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <div>Ada {{ $sessions->count() }} ujian tersedia. Tunggu sampai waktu mulai bila ujian belum aktif.</div>
            </div>
        @endif

        @if($sessions->count() > 0)
            <div class="row g-3">
                @foreach($sessions as $session)
                    @php
                        $start = $session->waktu_mulai?->timezone('Asia/Jakarta');
                        $end = $session->waktu_selesai?->timezone('Asia/Jakarta');
                        $result = $session->ujianResults->first();
                        $completed = $result && strtolower($result->status) === 'completed';
                        $classIds = is_array($session->ujian->class_ids) ? $session->ujian->class_ids : json_decode($session->ujian->class_ids ?? '[]', true) ?? [];
                        $classNames = collect($classIds)->map(function ($id) {
                            return \App\Models\SchoolClass::find($id)?->name ?? "Kelas $id";
                        })->filter()->implode(', ');
                    @endphp
                    <div class="col-md-6 col-lg-6">
                        <div class="card sesi-card h-100" data-start="{{ $start?->timestamp ?? 0 }}" data-end="{{ $end?->timestamp ?? 0 }}" data-completed="{{ $completed ? '1' : '0' }}" data-action-url="{{ route('siswa.ujian.mulai', $session) }}">
                            <div class="card-header bg-white border-bottom pb-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="text-primary small fw-semibold mb-1">{{ $session->ujian->course->nama_mata_pelajaran ?? '-' }}</div>
                                        <div class="fs-6 fw-bold">{{ $session->ujian->judul ?? '-' }}</div>
                                    </div>
                                    <span class="badge exam-status-badge bg-secondary badge-sm">Menunggu</span>
                                </div>
                            </div>
                            <div class="card-body py-3">
                                <p class="text-muted mb-2 small">{{ $session->ujian->deskripsi ?? 'Ujian dijadwalkan oleh admin untuk kelas Anda.' }}</p>
                                <div class="row g-2 mb-3">
                                    <div class="col-12">
                                        <div class="small text-muted">Waktu</div>
                                        <div class="small fw-semibold">
                                            <span class="exam-start-time">{{ $start?->format('d/m H:i') ?? '-' }}</span>
                                            <span class="text-muted">-</span>
                                            <span class="exam-end-time">{{ $end?->format('H:i') ?? '-' }} WIB</span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="small text-muted">Kelas</div>
                                        <div class="small fw-semibold">{{ $classNames ?: 'Semua kelas' }}</div>
                                    </div>
                                    <div class="col-12">
                                        <div class="small text-muted">Status</div>
                                        <div class="small fw-semibold exam-status-text">Menunggu</div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="small text-muted">Sisa Waktu</div>
                                    <div class="small fw-semibold text-warning exam-countdown">-</div>
                                </div>
                                <a href="#" class="btn btn-sm btn-secondary w-100 exam-action-btn disabled" aria-disabled="true">Belum Dimulai</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-light rounded-3">
                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-2">Belum ada ujian tersedia</h5>
                <p class="text-muted mb-0">Ujian akan muncul di sini ketika admin menjadwalkannya untuk kelas Anda.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .sesi-card {
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
    }
    .sesi-card:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    .badge-sm {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
    }
    .exam-countdown {
        font-size: 0.9rem;
        color: #ffc107;
    }
</style>
@endpush

@push('scripts')
<script>
    const jakartaTimeElement = document.getElementById('jakartaTime');
    const jakartaTimestampElement = document.getElementById('jakartaTimestamp');
    let currentServerTimestamp = Number(jakartaTimestampElement?.textContent || 0) * 1000;

    function formatDuration(seconds) {
        if (seconds <= 0) return '0d';
        const days = Math.floor(seconds / 86400);
        const hours = Math.floor((seconds % 86400) / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        const parts = [];
        if (days) parts.push(days + 'h');
        if (hours) parts.push(hours + 'j');
        if (minutes) parts.push(minutes + 'm');
        if (secs || parts.length === 0) parts.push(secs + 'd');
        return parts.join(' ');
    }

    function updateJakartaTime() {
        currentServerTimestamp += 1000;
        const now = new Date(currentServerTimestamp);
        const formatted = now.toLocaleString('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour12: false,
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        }).replace(',', '');
        if (jakartaTimeElement) {
            jakartaTimeElement.textContent = formatted + ' WIB';
        }
        if (jakartaTimestampElement) {
            jakartaTimestampElement.textContent = Math.floor(currentServerTimestamp / 1000);
        }
    }

    function updateExamCards() {
        document.querySelectorAll('.sesi-card').forEach(card => {
            const start = Number(card.dataset.start) * 1000;
            const end = Number(card.dataset.end) * 1000;
            const completed = card.dataset.completed === '1';
            const actionUrl = card.dataset.actionUrl;
            const statusText = card.querySelector('.exam-status-text');
            const statusBadge = card.querySelector('.exam-status-badge');
            const countdownText = card.querySelector('.exam-countdown');
            const actionBtn = card.querySelector('.exam-action-btn');

            let status = 'Ujian Selesai';
            let countdown = '-';
            let btnText = 'Ujian Selesai';
            let btnClass = 'btn-secondary';
            let btnDisabled = true;
            let badgeClass = 'bg-secondary';

            if (completed) {
                status = 'Sudah Dikerjakan';
                btnText = 'Ujian Selesai';
                badgeClass = 'bg-dark';
                countdown = 'Ujian telah selesai';
            } else if (currentServerTimestamp < start) {
                status = 'Belum Dimulai';
                btnText = 'Belum Dimulai';
                badgeClass = 'bg-info';
                countdown = 'Mulai dalam ' + formatDuration(Math.ceil((start - currentServerTimestamp) / 1000));
            } else if (currentServerTimestamp >= start && currentServerTimestamp <= end) {
                status = 'Sedang Berlangsung';
                btnText = 'Mulai Ujian';
                btnClass = 'btn-success';
                badgeClass = 'bg-success';
                btnDisabled = false;
                countdown = 'Sisa ' + formatDuration(Math.ceil((end - currentServerTimestamp) / 1000));
            } else {
                status = 'Ujian Selesai';
                btnText = 'Ujian Selesai';
                badgeClass = 'bg-danger';
                countdown = 'Waktu ujian telah berakhir';
            }

            if (statusText) statusText.textContent = status;
            if (statusBadge) {
                statusBadge.textContent = status;
                statusBadge.className = 'badge exam-status-badge badge-sm ' + badgeClass;
            }
            if (countdownText) countdownText.textContent = countdown;

            if (actionBtn) {
                actionBtn.textContent = btnText;
                actionBtn.className = 'btn btn-sm w-100 exam-action-btn ' + btnClass;
                if (btnDisabled) {
                    actionBtn.classList.add('disabled');
                    actionBtn.setAttribute('aria-disabled', 'true');
                    actionBtn.removeAttribute('href');
                } else {
                    actionBtn.classList.remove('disabled');
                    actionBtn.setAttribute('aria-disabled', 'false');
                    actionBtn.setAttribute('href', actionUrl);
                }
            }
        });
    }

    updateJakartaTime();
    updateExamCards();
    setInterval(() => {
        updateJakartaTime();
        updateExamCards();
    }, 1000);
</script>
@endpush

