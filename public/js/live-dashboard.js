/**
 * Enterprise-Level CBT: Real-Time Dashboard (Guru)
 * Membutuhkan Laravel Echo dan Pusher.js
 */

// Inisialisasi Echo (Gunakan Pusher atau Soketi)
// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     wsHost: window.location.hostname,
//     wsPort: 6001,
//     forceTLS: false,
//     disableStats: true,
// });

const EXAM_SESSION_ID = document.querySelector('meta[name="exam-session-id"]')?.content;

if (EXAM_SESSION_ID) {
    window.Echo.channel(`exam-session.${EXAM_SESSION_ID}`)
        .listen('StudentHeartbeatReceived', (e) => {
            console.log(`Student ${e.studentId} is ${e.status}`);
            
            // Update UI
            const studentRow = document.querySelector(`#student-${e.studentId}`);
            if (studentRow) {
                const statusBadge = studentRow.querySelector('.status-badge');
                statusBadge.textContent = 'Online';
                statusBadge.className = 'status-badge bg-success text-white px-2 py-1 rounded';
                
                // Update waktu terakhir aktif
                studentRow.setAttribute('data-last-active', Date.now());
            }
        })
        .listen('ExamViolationDetected', (e) => {
            console.warn(`Violation detected for Student ${e.studentId}: ${e.violationType}`);
            
            // Update UI
            const studentRow = document.querySelector(`#student-${e.studentId}`);
            if (studentRow) {
                const warningBadge = studentRow.querySelector('.warning-badge');
                warningBadge.textContent = `Pelanggaran: ${e.violationsCount}x`;
                warningBadge.style.display = 'inline-block';
                
                // Jika butuh notifikasi toast/alert
                // toastr.warning(`Siswa ID ${e.studentId} melakukan pelanggaran!`);
            }
        });

    // Pengecekan interval untuk siswa yang offline (Tidak ada heartbeat > 45 detik)
    setInterval(() => {
        const now = Date.now();
        document.querySelectorAll('.student-row').forEach(row => {
            const lastActive = parseInt(row.getAttribute('data-last-active') || 0);
            if (lastActive > 0 && (now - lastActive > 45000)) { // 45 detik
                const statusBadge = row.querySelector('.status-badge');
                statusBadge.textContent = 'Offline / Gangguan';
                statusBadge.className = 'status-badge bg-danger text-white px-2 py-1 rounded';
            }
        });
    }, 10000);
}
