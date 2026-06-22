/**
 * Enterprise-Level CBT: Client-Side Proctoring & Heartbeat
 */

// Konfigurasi ID
const EXAM_SESSION_ID = document.querySelector('meta[name="exam-session-id"]')?.content;
const STUDENT_ID = document.querySelector('meta[name="student-id"]')?.content;
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

// 1. HEARTBEAT SYSTEM (Ping server setiap 15 detik)
setInterval(() => {
    if(!EXAM_SESSION_ID) return;
    
    fetch('/api/exam/heartbeat', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            exam_session_id: EXAM_SESSION_ID,
            student_id: STUDENT_ID
        })
    }).catch(err => console.error('Heartbeat failed', err));
}, 15000);

// 2. TAB SWITCH / BLUR DETECTION (Anti-Cheat)
window.addEventListener('blur', () => {
    if(!EXAM_SESSION_ID) return;

    fetch('/api/exam/violation/log', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            exam_session_id: EXAM_SESSION_ID,
            student_id: STUDENT_ID,
            violation_type: 'tab_switch'
        })
    }).then(response => response.json())
      .then(data => {
          if (data.action === 'force_submit') {
              alert("Anda telah melanggar batas maksimal. Ujian dihentikan.");
              // Trigger auto-submit function here
              // submitExam();
          } else {
              alert("PERINGATAN! Anda terdeteksi keluar dari halaman ujian. Aktivitas ini dicatat!");
          }
      });
});

// 3. DISABLE RIGHT CLICK & COPY PASTE
document.addEventListener('contextmenu', event => event.preventDefault());
document.addEventListener('copy', event => {
    event.preventDefault();
    alert("Dilarang melakukan Copy-Paste!");
});
document.addEventListener('keydown', event => {
    // Disable F12, Ctrl+Shift+I, Ctrl+C, Ctrl+V
    if(event.key === 'F12' || (event.ctrlKey && ['c','v','i','u'].includes(event.key.toLowerCase()))) {
        event.preventDefault();
    }
});
