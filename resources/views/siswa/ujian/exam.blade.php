@extends('layouts.app')
@section('title', 'Kerjakan Ujian')
@section('page-title', 'Ujian')
@section('content')
<style>
    :root {
        --primary-blue: #007bff;
        --secondary-blue: #0056b3;
        --success-green: #28a745;
        --warning-yellow: #ffc107;
        --danger-red: #dc3545;
        --light-gray: #f4f7f6;
        --border-color: #dee2e6;
    }

    body {
        background-color: var(--light-gray);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .exam-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
        padding-bottom: 100px; /* Space for fixed footer */
    }

    /* Header Section */
    .exam-header-card {
        background: white;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .header-top {
        display: flex;
        align-items: center;
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .school-logo {
        width: 60px;
        height: 60px;
        margin-right: 20px;
        object-fit: contain;
    }

    .school-info h1 {
        font-size: 24px;
        font-weight: 800;
        margin: 0;
        color: #333;
    }

    .school-info p {
        font-size: 16px;
        margin: 0;
        color: #666;
        font-weight: 600;
    }

    .header-bottom {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        background: #fafafa;
        padding: 10px 20px;
    }

    .info-item {
        font-size: 14px;
        color: #555;
    }

    .info-item strong {
        color: #333;
    }

    /* Main Content Layout */
    .exam-main {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 20px;
    }

    /* Question Area */
    .question-card {
        background: white;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 30px;
        min-height: 500px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .question-number-header {
        font-size: 16px;
        font-weight: 800;
        color: #000;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .question-text {
        font-size: 16px;
        line-height: 1.8;
        color: #333;
        margin-bottom: 30px;
    }

    .options-list {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .option-container {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 8px 0;
        border: none;
        border-radius: 0;
        transition: all 0.2s;
        cursor: pointer;
    }

    .option-container:hover {
        background-color: transparent;
    }

    .option-container input[type="radio"] {
        appearance: none;
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #adb5bd;
        border-radius: 50%;
        margin-top: 2px;
        cursor: pointer;
        position: relative;
        flex-shrink: 0;
        background: #fff;
    }

    .option-container input[type="radio"]:checked {
        border-color: var(--primary-blue);
        background-color: #fff;
    }

    .option-container input[type="radio"]:checked::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 10px;
        height: 10px;
        background-color: var(--primary-blue);
        border-radius: 50%;
    }

    .option-text {
        font-size: 15px;
        color: #444;
        cursor: pointer;
        flex: 1;
    }

    /* Sidebar Area */
    .sidebar-card {
        background: white;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .timer-section {
        text-align: center;
        margin-bottom: 20px;
    }

    .timer-label {
        font-size: 14px;
        font-weight: 700;
        color: #666;
        display: block;
        margin-bottom: 5px;
    }

    .timer-value {
        font-size: 32px;
        font-weight: 800;
        color: #333;
        font-family: 'Courier New', Courier, monospace;
    }

    .timer-value.warning {
        color: var(--danger-red);
    }

    .btn-student-info {
        width: 100%;
        background: #f0f2f5;
        border: 1px solid #ddd;
        color: #555;
        font-size: 13px;
        font-weight: 600;
        padding: 8px;
        border-radius: 6px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .peta-soal-title {
        font-size: 15px;
        font-weight: 800;
        margin-bottom: 15px;
        color: #333;
    }

    .question-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
    }

    .nav-btn {
        aspect-ratio: 1;
        border: 1px solid #ddd;
        background: #e9ecef;
        color: #444;
        font-size: 13px;
        font-weight: 700;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }

    .nav-btn:hover {
        background: #dee2e6;
    }

    .nav-btn.current {
        background: var(--primary-blue);
        color: white;
        border-color: var(--primary-blue);
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.25);
    }

    .nav-btn.answered {
        background: var(--success-green);
        color: white;
        border-color: var(--success-green);
    }

    .nav-btn.unsure {
        background: var(--warning-yellow);
        color: #333;
        border-color: var(--warning-yellow);
    }

    .nav-btn.unsure::after {
        content: "\f024";
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        font-size: 8px;
        position: absolute;
        top: 2px;
        right: 2px;
    }

    /* Fixed Footer Navigation */
    .footer-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: white;
        padding: 15px 0;
        box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    .footer-content {
        max-width: 1000px;
        margin: 0 auto;
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        padding: 0 20px;
    }

    .btn-nav-main {
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 800;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn-prev-soal {
        background: #dee2e6;
        color: #495057;
    }

    .btn-prev-soal:hover:not(:disabled) {
        background: #ced4da;
    }

    .btn-unsure-soal {
        background: white;
        color: #333;
        border: 2px solid #333;
    }

    .btn-unsure-soal.active {
        background: var(--warning-yellow);
        border-color: var(--warning-yellow);
    }

    .btn-next-soal {
        background: var(--primary-blue);
        color: white;
    }

    .btn-next-soal:hover:not(:disabled) {
        background: var(--secondary-blue);
    }

    .btn-finish-soal {
        background: var(--success-green);
        color: white;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 12px;
        border: none;
    }

    .modal-header {
        background: var(--primary-blue);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .auto-save-indicator {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 2000;
        background: rgba(255, 255, 255, 0.9);
        padding: 8px 15px;
        border-radius: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: none;
        align-items: center;
        gap: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .auto-save-indicator.show {
        display: flex;
    }

    /* Prevent text selection */
    .unselectable {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    @media (max-width: 992px) {
        .exam-main {
            grid-template-columns: 1fr;
        }
        .header-bottom {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Auto-Save Indicator -->
<div id="autoSaveIndicator" class="auto-save-indicator">
    <i class="fas fa-sync-alt fa-spin text-primary"></i>
    <span>Menyimpan...</span>
</div>

<div class="exam-container unselectable">
    <!-- Header Section -->
    <div class="exam-header-card">
        <div class="header-top">
            <img src="{{ asset('bg/logo.png') }}" class="school-logo" alt="Logo">
            <div class="school-info">
                <h1>SMK YADIKA 13 - TAMBUN</h1>
                <p>{{ $sesi->ujian->judul ?? 'UJIAN ONLINE' }}</p>
            </div>
        </div>
        <div class="header-bottom">
            <div class="info-item">Mata Pelajaran: <strong>{{ $sesi->ujian->course->nama_mata_pelajaran ?? '-' }}</strong></div>
            <div class="info-item">Class: <strong>{{ auth()->user()->schoolClass->name ?? '-' }}</strong></div>
            <div class="info-item">Teacher: <strong>{{ $sesi->ujian->course->guru->name ?? '-' }}</strong></div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="exam-main">
        <!-- Question Area -->
        <div class="question-column">
            <div class="question-card">
                <div class="question-number-header">
                    SOAL NO. <span id="current-q-num">1</span> DARI {{ count($questions) }}
                </div>
                <div id="questionText" class="question-text">
                    <!-- Loaded via JS -->
                </div>
                <div id="optionsContainer" class="options-list">
                    <!-- Loaded via JS -->
                </div>
            </div>
        </div>

        <!-- Sidebar Area -->
        <div class="sidebar-column">
            <div class="sidebar-card">
                <div class="timer-section">
                    <span class="timer-label">SISA WAKTU:</span>
                    <div id="time-left" class="timer-value">00:00:00</div>
                </div>
                
                <button class="btn-student-info" onclick="showStudentInfo()">
                    <i class="fas fa-info-circle"></i> Student Info
                </button>

                <div class="peta-soal-title">Peta Soal</div>
                <div id="navButtons" class="question-grid">
                    <!-- Generated via JS -->
                </div>
            </div>

            <!-- Anti-Cheating Info -->
            <div class="sidebar-card" style="padding: 10px; background: #fff9db; border-color: #ffe066;">
                <div class="text-center" style="font-size: 11px; color: #856404; font-weight: 600;">
                    <i class="fas fa-shield-alt me-1"></i> Mode Ujian Aman Aktif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Footer -->
<div class="footer-nav">
    <div class="footer-content">
        <button type="button" class="btn-nav-main btn-prev-soal" id="prevBtn" onclick="prevQuestion()">
            <i class="fas fa-arrow-left"></i> SEBELUMNYA
        </button>
        <button type="button" class="btn-nav-main btn-unsure-soal" id="unsureBtn" onclick="markUnsure()">
            <i class="fas fa-flag"></i> RAGU-RAGU
        </button>
        <button type="button" class="btn-nav-main btn-next-soal" id="nextBtn" onclick="nextQuestion()">
            SELANJUTNYA <i class="fas fa-arrow-right"></i>
        </button>
        <button type="button" class="btn-nav-main btn-finish-soal d-none" id="finishBtn" onclick="showExamConfirmModal()">
            <i class="fas fa-check-circle"></i> SELESAI UJIAN
        </button>
    </div>
</div>

<!-- Student Info Modal -->
<div class="modal fade" id="studentInfoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Student Information</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center mb-4">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px; font-weight: bold;">
                        {{ substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="ms-3">
                        <h5 class="mb-0 fw-bold">{{ auth()->user()->name }}</h5>
                        <p class="text-muted mb-0">NIS: {{ auth()->user()->nis_nip ?? '-' }}</p>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Kelas</small>
                            <span class="fw-bold">{{ auth()->user()->schoolClass->name ?? '-' }}</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 border rounded bg-light">
                            <small class="text-muted d-block">Jurusan</small>
                            <span class="fw-bold">{{ auth()->user()->schoolClass->major->name ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="examConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Selesai</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 60px;"></i>
                <h4 class="fw-bold">Yakin ingin mengakhiri ujian?</h4>
                <p class="text-muted">Pastikan semua soal telah terjawab dengan benar. Setelah dikirim, Anda tidak dapat mengubah jawaban lagi.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">KEMBALI</button>
                <button type="button" class="btn btn-success px-4 fw-bold" id="confirmSubmitBtn" onclick="submitExam()">SELESAI</button>
            </div>
        </div>
    </div>
</div>

<form id="examForm" method="POST" action="{{ route('siswa.ujian.submit', $sesi->id) }}" style="display:none;">
    @csrf
</form>
@push('scripts')
<script>
let timeLeft = {{ $durasi * 60 }};
let timerInterval;
let currentQuestionIndex = 0;
let unsureQuestions = new Set();
let answers = {!! json_encode($savedAnswers ?? []) !!}; 
let isSubmitting = false; 
const resultId = {{ $result->id }};
const examSessionId = {{ $sesi->id }};
const studentId = {{ auth()->id() }};
const submitUrl = '{{ route("siswa.ujian.submit", $sesi->id) }}';
// REMOVED strip_tags to support HTML/Rich Text in questions
const questions = {!! json_encode($questions->map(function($q) { return ['id' => $q->id, 'pertanyaan' => $q->pertanyaan, 'jawaban_a' => $q->jawaban_a, 'jawaban_b' => $q->jawaban_b, 'jawaban_c' => $q->jawaban_c, 'jawaban_d' => $q->jawaban_d, 'jawaban_e' => $q->jawaban_e, 'kunci_jawaban' => $q->kunci_jawaban]; })) !!};
const totalQuestions = questions.length;

function updateTimer() {
    const hours = Math.floor(timeLeft / 3600);
    const minutes = Math.floor((timeLeft % 3600) / 60);
    const seconds = timeLeft % 60;
    
    const display = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    document.getElementById('time-left').textContent = display;
    
    if (timeLeft <= 300) document.getElementById('time-left').classList.add('warning');
    if (timeLeft <= 0) { 
        clearInterval(timerInterval); 
        submitExam(true); 
    } else {
        timeLeft--;
    }
}

function displayQuestion(index) {
    const question = questions[index];
    document.getElementById('questionText').innerHTML = question.pertanyaan;
    
    const optionsContainer = document.getElementById('optionsContainer');
    optionsContainer.innerHTML = '';
    
    const options = [
        {key:'A', text:question.jawaban_a},
        {key:'B', text:question.jawaban_b},
        {key:'C', text:question.jawaban_c},
        {key:'D', text:question.jawaban_d},
        {key:'E', text:question.jawaban_e}
    ];
    
    const savedValue = answers[question.id] ?? null;
    
    options.forEach(opt => {
        if (opt.text) {
            const container = document.createElement('label');
            container.className = 'option-container';
            container.htmlFor = `q${question.id}${opt.key}`;
            
            const input = document.createElement('input');
            input.type = 'radio';
            input.name = `answers[${question.id}]`;
            input.value = opt.key;
            input.id = `q${question.id}${opt.key}`;
            if (savedValue === opt.key) input.checked = true;
            
            input.addEventListener('change', () => {
                answers[question.id] = opt.key;
                updateNavigation();
                clearTimeout(autoSaveTimeout);
                autoSaveTimeout = setTimeout(autoSaveAnswer, 500);
            });
            
            const textSpan = document.createElement('span');
            textSpan.className = 'option-text';
            // Use letter with dot and bold it to match the requested style
            textSpan.innerHTML = `<span style="font-weight: 800;">${opt.key}.</span> ${opt.text}`;
            
            container.appendChild(input);
            container.appendChild(textSpan);
            optionsContainer.appendChild(container);
        }
    });

    document.getElementById('current-q-num').textContent = index + 1;
    
    // Navigation visibility
    document.getElementById('prevBtn').disabled = index === 0;
    if (index === totalQuestions - 1) {
        document.getElementById('nextBtn').classList.add('d-none');
        document.getElementById('finishBtn').classList.remove('d-none');
    } else {
        document.getElementById('nextBtn').classList.remove('d-none');
        document.getElementById('finishBtn').classList.add('d-none');
    }

    // Update Ragu-Ragu button state
    const unsureBtn = document.getElementById('unsureBtn');
    if (unsureQuestions.has(question.id)) {
        unsureBtn.classList.add('active');
    } else {
        unsureBtn.classList.remove('active');
    }

    updateNavigation();
}

function updateNavigation() {
    document.querySelectorAll('.nav-btn').forEach((btn, idx) => {
        btn.classList.remove('current', 'answered', 'unsure');
        const questionId = questions[idx].id;
        const isAnswered = answers[questionId] !== undefined;
        
        if (idx === currentQuestionIndex) {
            btn.classList.add('current');
        }
        
        if (unsureQuestions.has(questionId)) {
            btn.classList.add('unsure');
        } else if (isAnswered) {
            btn.classList.add('answered');
        }
    });
}

function nextQuestion() {
    if (currentQuestionIndex < totalQuestions - 1) {
        currentQuestionIndex++;
        displayQuestion(currentQuestionIndex);
    }
}

function prevQuestion() {
    if (currentQuestionIndex > 0) {
        currentQuestionIndex--;
        displayQuestion(currentQuestionIndex);
    }
}

function markUnsure() {
    const questionId = questions[currentQuestionIndex].id;
    if (unsureQuestions.has(questionId)) {
        unsureQuestions.delete(questionId);
    } else {
        unsureQuestions.add(questionId);
    }
    displayQuestion(currentQuestionIndex); // Refresh UI
}

function restoreSavedAnswers() {
    // If we have answers from server, use them
    if (Object.keys(answers).length > 0) return;

    try {
        const saved = localStorage.getItem('exam_answers_' + examSessionId);
        if (saved) {
            const parsed = JSON.parse(saved);
            if (parsed && typeof parsed === 'object') {
                answers = parsed;
            }
        }
    } catch (e) {
        console.warn('LocalStorage error');
    }
}

function showStudentInfo() {
    const modal = new bootstrap.Modal(document.getElementById('studentInfoModal'));
    modal.show();
}

function showExamConfirmModal() {
    const modal = new bootstrap.Modal(document.getElementById('examConfirmModal'));
    modal.show();
}

function submitExam(isTimeout = false) {
    if (isSubmitting) return;
    isSubmitting = true;
    
    if (timerInterval) clearInterval(timerInterval);

    // Visual feedback on the button
    const confirmBtn = document.getElementById('confirmSubmitBtn');
    if (confirmBtn) {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>MENGIRIM...';
    }

    // Tutup modal langsung untuk memberikan respon instan
    const modalEl = document.getElementById('examConfirmModal');
    if (modalEl) {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();
    }

    const csrfToken = document.querySelector('input[name="_token"]')?.value;

    fetch(submitUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ answers })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Sesuai permintaan terbaru: langsung redirect ke daftar ujian tanpa download otomatis
            // Siswa bisa mendownload PDF secara manual melalui tombol yang sudah disediakan di daftar ujian
            window.location.href = data.redirectUrl;
        } else {
            alert(data.message || 'Gagal mengirim jawaban.');
            isSubmitting = false;
            if (confirmBtn) {
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'SELESAI';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi atau server saat mengirim jawaban.');
        isSubmitting = false;
        if (confirmBtn) {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = 'SELESAI';
        }
    });
}



let autoSaveTimeout;
function autoSaveAnswer() {
    const indicator = document.getElementById('autoSaveIndicator');
    indicator.classList.add('show');
    
    const currentQuestion = questions[currentQuestionIndex];
    const selectedAnswer = answers[currentQuestion.id];
    
    if (!selectedAnswer) {
        indicator.classList.remove('show');
        return;
    }

    // Save to local storage first
    try {
        localStorage.setItem('exam_answers_' + examSessionId, JSON.stringify(answers));
    } catch (e) {}

    const csrfToken = document.querySelector('input[name="_token"]')?.value;

    fetch('{{ route("api.exam.autosave") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            exam_session_id: examSessionId,
            ujian_result_id: resultId,
            soal_id: currentQuestion.id,
            option_id: selectedAnswer,
            student_id: studentId
        })
    })
    .then(() => {
        setTimeout(() => indicator.classList.remove('show'), 1000);
    })
    .catch(() => {
        indicator.classList.remove('show');
    });
}

// Anti-Cheating Logic
function logViolation(type) {
    const csrfToken = document.querySelector('input[name="_token"]')?.value;
    fetch('{{ route("api.exam.violation") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({
            exam_session_id: examSessionId,
            student_id: studentId,
            violation_type: type
        })
    }).catch(() => {});
}

window.addEventListener('blur', () => {
    if (!isSubmitting) {
        logViolation('window-blur');
        alert('DILARANG meninggalkan halaman ujian!');
    }
});

document.addEventListener('visibilitychange', () => {
    if (document.hidden && !isSubmitting) {
        logViolation('tab-switch');
    }
});

document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && ['c','v','x','p'].includes(e.key.toLowerCase())) {
        e.preventDefault();
        logViolation('prohibited-key');
    }
});

window.addEventListener('beforeunload', e => {
    if (!isSubmitting) {
        e.preventDefault();
        e.returnValue = '';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // Generate navigation grid
    const navButtons = document.getElementById('navButtons');
    questions.forEach((_, idx) => {
        const btn = document.createElement('button');
        btn.className = 'nav-btn';
        btn.textContent = idx + 1;
        btn.onclick = () => {
            currentQuestionIndex = idx;
            displayQuestion(idx);
        };
        navButtons.appendChild(btn);
    });

    restoreSavedAnswers();
    displayQuestion(0);
    timerInterval = setInterval(updateTimer, 1000);
});
</script>
@endpush
@endsection
