@extends('layouts.app')
@section('title', 'Kerjakan Ujian')
@section('page-title', 'Ujian')
@section('content')
<style>
    .exam-wrapper { background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); overflow: hidden; }
    .exam-header { display: flex; justify-content: space-between; align-items: center; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
    .exam-header-left { flex: 1; }
    .exam-header-left h5 { margin: 0; font-size: 18px; margin-bottom: 5px; }
    .exam-header-left small { opacity: 0.9; }
    .timer-display { background: rgba(255,255,255,0.2); padding: 10px 20px; border-radius: 20px; font-weight: bold; border: 1px solid rgba(255,255,255,0.3); font-size: 16px; }
    .timer-display.warning { background: #ff6b6b; border-color: #ff5252; }
    .exam-content { display: grid; grid-template-columns: 1fr 200px; gap: 20px; padding: 30px; min-height: calc(100vh - 300px); }
    .question-display-area { display: flex; flex-direction: column; }
    .question-info { text-align: center; margin-bottom: 20px; font-size: 14px; font-weight: bold; color: #667eea; }
    .question-wrapper { background: #f8f9fa; padding: 30px; border-radius: 8px; border: 2px solid #e8e8e8; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
    .question-content { margin-bottom: 30px; }
    .question-text { font-size: 18px; font-weight: bold; color: #333; margin-bottom: 30px; line-height: 1.6; }
    .question-options { display: flex; flex-direction: column; gap: 12px; }
    .option-item { background: white; padding: 15px; border: 2px solid #ddd; border-radius: 6px; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 12px; }
    .option-item:hover { border-color: #667eea; background: #f8f9ff; }
    .option-item input[type="radio"] { cursor: pointer; width: 20px; height: 20px; }
    .option-item input[type="radio"]:checked + label { font-weight: bold; color: #667eea; }
    .option-label { cursor: pointer; flex: 1; font-weight: 500; color: #333; }
    .question-nav { display: flex; justify-content: space-between; gap: 15px; margin-top: 30px; }
    .btn-prev, .btn-next { flex: 1; padding: 12px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; transition: all 0.3s ease; }
    .btn-prev { background: #6c757d; color: white; }
    .btn-prev:hover:not(:disabled) { background: #5a6268; }
    .btn-prev:disabled { opacity: 0.5; cursor: not-allowed; }
    .btn-next { background: #667eea; color: white; }
    .btn-next:hover:not(:disabled) { background: #5568d3; }
    .btn-next:disabled { opacity: 0.5; cursor: not-allowed; }
    .sidebar { display: flex; flex-direction: column; gap: 15px; }
    .nav-questions-box { background: white; padding: 15px; border-radius: 8px; border: 2px solid #e8e8e8; }
    .nav-questions-box h6 { margin: 0 0 12px 0; font-size: 13px; font-weight: bold; color: #667eea; display: flex; align-items: center; gap: 8px; }
    .nav-divider { height: 2px; background: #667eea; margin-bottom: 12px; }
    .nav-buttons-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 6px; }
    .nav-btn { aspect-ratio: 1; padding: 0; border: 2px solid #ddd; background: white; cursor: pointer; border-radius: 6px; font-size: 12px; font-weight: bold; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; }
    .nav-btn:hover { border-color: #667eea; background: #f8f9ff; }
    .nav-btn.answered { background: #d4edda; border-color: #28a745; color: #155724; }
    .nav-btn.current { background: #667eea; color: white; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3); }
    .nav-btn.unsure { background: #fff3cd; border-color: #ffc107; color: #856404; }
    .action-buttons { background: white; padding: 15px; border-radius: 8px; border: 2px solid #e8e8e8; display: flex; flex-direction: column; gap: 10px; }
    .btn-action { width: 100%; padding: 12px; border: none; border-radius: 6px; font-size: 12px; font-weight: bold; cursor: pointer; transition: all 0.3s ease; }
    .btn-unsure { background: #fff3cd; color: #856404; border: 2px solid #ffc107; }
    .btn-unsure:hover { background: #ffc107; color: white; }
    .btn-finish { background: #28a745; color: white; border: 2px solid #28a745; }
    .btn-finish:hover { background: #218838; border-color: #218838; }
    
    /* Custom Confirmation Modal */
    .exam-confirm-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.5);
        animation: fadeIn 0.3s ease-in-out;
    }
    .exam-confirm-modal.show {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    .exam-confirm-content {
        background: white;
        padding: 30px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        max-width: 500px;
        width: 90%;
        animation: slideUp 0.3s ease-out;
    }
    @keyframes slideUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    .exam-confirm-icon {
        font-size: 48px;
        color: #ff6b6b;
        margin-bottom: 15px;
        text-align: center;
    }
    .exam-confirm-title {
        font-size: 20px;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        text-align: center;
    }
    .exam-confirm-message {
        font-size: 14px;
        color: #666;
        margin-bottom: 25px;
        text-align: center;
        line-height: 1.6;
    }
    .exam-confirm-buttons {
        display: flex;
        gap: 12px;
        justify-content: center;
    }
    .exam-btn-cancel {
        flex: 1;
        padding: 12px 24px;
        border: 2px solid #6c757d;
        background: white;
        color: #6c757d;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .exam-btn-cancel:hover {
        background: #f8f9fa;
        border-color: #5a6268;
        color: #5a6268;
    }
    .exam-btn-submit {
        flex: 1;
        padding: 12px 24px;
        border: 2px solid #28a745;
        background: #28a745;
        color: white;
        border-radius: 6px;
        font-weight: bold;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .exam-btn-submit:hover {
        background: #218838;
        border-color: #218838;
    }
</style>

<!-- Custom Confirmation Modal -->
<div id="examConfirmModal" class="exam-confirm-modal">
    <div class="exam-confirm-content">
        <div class="exam-confirm-icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <div class="exam-confirm-title">Konfirmasi Selesai Ujian</div>
        <div class="exam-confirm-message">
            Halaman ini meminta Anda untuk mengkonfirmasi bahwa Anda Telah Menyelesaikan Ujian — Periksa Kembali Soal Dan Jawab Anda sebelum Klik Selesai
        </div>
        <div class="exam-confirm-buttons">
            <button type="button" class="exam-btn-cancel" id="examBtnCancel">
                <i class="fas fa-times me-2"></i>Cancel
            </button>
            <button type="button" class="exam-btn-submit" id="examBtnConfirm">
                <i class="fas fa-check me-2"></i>Selesai
            </button>
        </div>
    </div>
</div>

<div class="exam-wrapper">
    <div class="exam-header">
        <div class="exam-header-left">
            <h5>Kerjakan Ujian</h5>
            <small>{{ $sesi->ujian->judul ?? 'Ujian' }}</small>
        </div>
        <div class="timer-display" id="timerBadge">
            <i class="fas fa-clock me-2"></i>
            <span id="time-left">{{ $durasi }}:00</span>
        </div>
    </div>
    <div class="exam-content">
        <div class="question-display-area">
            <div class="question-info">
                Soal <span id="current-q">1</span> dari {{ count($questions) }}
            </div>
            <form id="examForm" method="POST" action="{{ route('siswa.ujian.submit', $sesi->id) }}">
                @csrf
                <div class="question-wrapper">
                    <div class="question-content">
                        <div class="question-text" id="questionText"></div>
                        <div class="question-options" id="optionsContainer"></div>
                    </div>
                    <div class="question-nav">
                        <button type="button" class="btn-prev" id="prevBtn" onclick="prevQuestion()">
                            <i class="fas fa-arrow-left me-2"></i> Sebelumnya
                        </button>
                        <button type="button" class="btn-next" id="nextBtn" onclick="nextQuestion()">
                            Selanjutnya <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="sidebar">
            <div class="nav-questions-box">
                <h6><i class="fas fa-tasks"></i> Navigasi Soal</h6>
                <div class="nav-divider"></div>
                <div class="nav-buttons-grid" id="navButtons"></div>
            </div>
            <div class="action-buttons">
                <button type="button" class="btn-action btn-unsure" id="unsureBtn" onclick="markUnsure()">
                    <i class="fas fa-question-circle me-1"></i>
                    Ragu-Ragu
                </button>
                <button type="submit" form="examForm" class="btn-action btn-finish" id="submitBtn">
                    <i class="fas fa-check-circle me-1"></i>
                    Selesaikan Ujian
                </button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
let timeLeft = {{ $durasi * 60 }};
let timerInterval;
let currentQuestionIndex = 0;
let unsureQuestions = new Set();
let answers = {}; // Store all student answers
let isSubmitting = false; // Flag to prevent beforeunload during submit
const questions = {!! json_encode($questions->map(function($q) { return ['id' => $q->id, 'pertanyaan' => strip_tags($q->pertanyaan), 'jawaban_a' => $q->jawaban_a, 'jawaban_b' => $q->jawaban_b, 'jawaban_c' => $q->jawaban_c, 'jawaban_d' => $q->jawaban_d, 'jawaban_e' => $q->jawaban_e, 'kunci_jawaban' => $q->kunci_jawaban]; })) !!};
const totalQuestions = questions.length;
function updateTimer() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('time-left').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    if (timeLeft <= 300) document.getElementById('timerBadge').classList.add('warning');
    if (timeLeft <= 0) { clearInterval(timerInterval); document.getElementById('examForm').submit(); }
    else timeLeft--;
}
function displayQuestion(index) {
    const question = questions[index];
    document.getElementById('questionText').innerHTML = `<strong>${index + 1}. ${question.pertanyaan}</strong>`;
    const optionsContainer = document.getElementById('optionsContainer');
    optionsContainer.innerHTML = '';
    const options = [{key:'A',text:question.jawaban_a},{key:'B',text:question.jawaban_b},{key:'C',text:question.jawaban_c},{key:'D',text:question.jawaban_d},{key:'E',text:question.jawaban_e}];
    
    // Get saved answer from our answers object
    const savedValue = answers[question.id] || null;
    
    options.forEach(opt => {
        if (opt.text) {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'option-item';
            const input = document.createElement('input');
            input.type = 'radio';
            input.name = `answers[${question.id}]`;
            input.value = opt.key;
            input.id = `q${question.id}${opt.key}`;
            // Check if this option matches saved answer
            if (savedValue === opt.key) input.checked = true;
            // When option changes, save to answers object AND form
            input.addEventListener('change', () => {
                answers[question.id] = opt.key;
                // Also update form value
                document.querySelector(`input[name="answers[${question.id}]"]:checked`).value = opt.key;
                updateNavigation();
            });
            const label = document.createElement('label');
            label.htmlFor = `q${question.id}${opt.key}`;
            label.className = 'option-label';
            label.innerHTML = `<strong>${opt.key}.</strong> ${opt.text}`;
            optionDiv.appendChild(input);
            optionDiv.appendChild(label);
            optionsContainer.appendChild(optionDiv);
        }
    });
    document.getElementById('current-q').textContent = index + 1;
    document.getElementById('prevBtn').disabled = index === 0;
    document.getElementById('nextBtn').disabled = index === totalQuestions - 1;
    updateNavigation();
}
function updateNavigation() {
    document.querySelectorAll('.nav-btn').forEach((btn, idx) => {
        btn.classList.remove('current', 'answered', 'unsure');
        const questionId = questions[idx].id;
        // Check if question is answered (from answers object)
        const isAnswered = answers[questionId] !== undefined;
        if (idx === currentQuestionIndex) btn.classList.add('current');
        else if (unsureQuestions.has(questionId)) btn.classList.add('unsure');
        else if (isAnswered) btn.classList.add('answered');
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
    unsureQuestions.add(questionId);
    if (currentQuestionIndex < totalQuestions - 1) nextQuestion();
    updateNavigation();
}
document.addEventListener('DOMContentLoaded', function() {
    questions.forEach((q, idx) => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'nav-btn';
        btn.textContent = idx + 1;
        btn.addEventListener('click', (e) => { e.preventDefault(); currentQuestionIndex = idx; displayQuestion(idx); });
        document.getElementById('navButtons').appendChild(btn);
    });
    timerInterval = setInterval(updateTimer, 1000);
    displayQuestion(0);
    
    // Prevent leaving the page while exam is active
    window.addEventListener('beforeunload', function(e) {
        // Don't show warning if already submitting
        if (timerInterval && !isSubmitting) { 
            e.preventDefault(); 
            e.returnValue = 'Ujian sedang berlangsung. Yakin ingin keluar?'; 
        }
    });
    
    // Show custom modal when submit button is clicked
    document.getElementById('submitBtn').addEventListener('click', function(e) { 
        e.preventDefault();
        showExamConfirmModal();
    });
    
    // Cancel button
    document.getElementById('examBtnCancel').addEventListener('click', function() {
        hideExamConfirmModal();
    });
    
    // Confirm button
    document.getElementById('examBtnConfirm').addEventListener('click', function() {
        // Set flag to prevent beforeunload
        isSubmitting = true;
        
        // Clear timer first
        if (timerInterval) {
            clearInterval(timerInterval);
        }
        
        // Sync all answers from answers object to form before submit
        for (let questionId in answers) {
            const input = document.querySelector(`input[name="answers[${questionId}]"][value="${answers[questionId]}"]`);
            if (input) input.checked = true;
        }
        
        // Submit form via AJAX to get JSON response
        const form = document.getElementById('examForm');
        const formData = new FormData(form);
        
        // Get CSRF token from form input or meta tag
        let csrfToken = document.querySelector('input[name="_token"]')?.value;
        if (!csrfToken) {
            csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        }
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // If PDF should be downloaded
                if (data.shouldDownloadPdf && data.pdfUrl) {
                    // Trigger PDF download
                    const link = document.createElement('a');
                    link.href = data.pdfUrl;
                    link.download = true;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // Redirect after a short delay to allow download to start
                    setTimeout(() => {
                        window.location.href = data.redirectUrl;
                    }, 1000);
                } else {
                    // Just redirect
                    window.location.href = data.redirectUrl;
                }
            } else {
                // Handle error
                const errorMsg = data.message || 'Terjadi kesalahan saat menyelesaikan ujian. Silakan refresh halaman dan coba lagi.';
                alert(errorMsg);
                // Reload page to get fresh CSRF token if needed
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan: ' + error.message);
            setTimeout(() => {
                window.location.href = '{{ route("siswa.ujian") }}';
            }, 1000);
        });
    });
    
    // Close modal when clicking outside
    document.getElementById('examConfirmModal').addEventListener('click', function(e) {
        if (e.target === this) {
            hideExamConfirmModal();
        }
    });
});

function showExamConfirmModal() {
    document.getElementById('examConfirmModal').classList.add('show');
}

function hideExamConfirmModal() {
    document.getElementById('examConfirmModal').classList.remove('show');
}
</script>
@endpush
@endsection
