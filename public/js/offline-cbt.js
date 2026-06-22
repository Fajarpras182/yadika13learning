/**
 * Enterprise-Level CBT: Offline Mode with IndexedDB & Background Sync
 * 
 * Ketika koneksi internet putus:
 * 1. Jawaban disimpan ke IndexedDB (browser lokal)
 * 2. Ketika koneksi pulih, jawaban di-sync massal ke server
 */

const DB_NAME = 'cbt_offline_db';
const DB_VERSION = 1;
const STORE_NAME = 'pending_answers';

// ============================================================
// 1. INDEXEDDB SETUP
// ============================================================

function openDatabase() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                const store = db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
                store.createIndex('exam_session_id', 'exam_session_id', { unique: false });
                store.createIndex('soal_id', 'soal_id', { unique: false });
            }
        };

        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

/**
 * Menyimpan jawaban ke IndexedDB (saat offline)
 */
async function saveAnswerOffline(examSessionId, soalId, optionId, isDoubtful = false) {
    const db = await openDatabase();
    const tx = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.objectStore(STORE_NAME);

    // Cek apakah sudah ada jawaban untuk soal ini, update jika ada
    const index = store.index('soal_id');
    const existing = await new Promise((resolve) => {
        const req = index.openCursor(IDBKeyRange.only(soalId));
        req.onsuccess = (e) => resolve(e.target.result);
    });

    if (existing) {
        existing.value.option_id = optionId;
        existing.value.is_doubtful = isDoubtful;
        existing.value.timestamp = Date.now();
        existing.update(existing.value);
    } else {
        store.add({
            exam_session_id: examSessionId,
            soal_id: soalId,
            option_id: optionId,
            is_doubtful: isDoubtful,
            timestamp: Date.now(),
            synced: false,
        });
    }

    await new Promise((resolve, reject) => {
        tx.oncomplete = resolve;
        tx.onerror = reject;
    });
}

/**
 * Mengambil semua jawaban pending dari IndexedDB
 */
async function getPendingAnswers() {
    const db = await openDatabase();
    const tx = db.transaction(STORE_NAME, 'readonly');
    const store = tx.objectStore(STORE_NAME);

    return new Promise((resolve, reject) => {
        const req = store.getAll();
        req.onsuccess = () => resolve(req.result);
        req.onerror = () => reject(req.error);
    });
}

/**
 * Menghapus semua jawaban yang sudah berhasil disinkronkan
 */
async function clearSyncedAnswers() {
    const db = await openDatabase();
    const tx = db.transaction(STORE_NAME, 'readwrite');
    const store = tx.objectStore(STORE_NAME);
    store.clear();
    return new Promise((resolve) => { tx.oncomplete = resolve; });
}

// ============================================================
// 2. NETWORK STATUS DETECTION & AUTO-SYNC
// ============================================================

const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content;

/**
 * Sinkronisasi jawaban offline ke server (Bulk Sync)
 */
async function syncPendingAnswers() {
    const pendingAnswers = await getPendingAnswers();

    if (pendingAnswers.length === 0) return;

    console.log(`[Offline CBT] Sinkronisasi ${pendingAnswers.length} jawaban...`);

    try {
        // Kirim satu per satu ke API auto-save (atau bisa dibuat endpoint bulk)
        for (const answer of pendingAnswers) {
            await fetch('/api/exam/answer/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    exam_session_id: answer.exam_session_id,
                    soal_id: answer.soal_id,
                    option_id: answer.option_id,
                }),
            });
        }

        // Berhasil semua: hapus data offline
        await clearSyncedAnswers();
        console.log('[Offline CBT] Semua jawaban berhasil disinkronkan!');

        // Notifikasi ke siswa
        showSyncNotification('success', 'Koneksi pulih! Semua jawaban berhasil disinkronkan ke server.');

    } catch (error) {
        console.error('[Offline CBT] Gagal sinkronisasi:', error);
        showSyncNotification('error', 'Gagal sinkronisasi. Jawaban masih aman tersimpan di perangkat Anda.');
    }
}

/**
 * Menampilkan notifikasi sync ke UI
 */
function showSyncNotification(type, message) {
    const notif = document.createElement('div');
    notif.className = `sync-notification sync-${type}`;
    notif.innerHTML = `<span>${message}</span>`;
    notif.style.cssText = `
        position: fixed; top: 20px; right: 20px; z-index: 99999;
        padding: 16px 24px; border-radius: 8px; font-size: 14px;
        color: #fff; max-width: 400px; box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        animation: slideIn 0.3s ease-out;
    `;
    document.body.appendChild(notif);
    setTimeout(() => notif.remove(), 5000);
}

// Event: Koneksi pulih -> langsung sync
window.addEventListener('online', () => {
    console.log('[Offline CBT] Koneksi kembali online. Memulai sinkronisasi...');
    syncPendingAnswers();
});

// Event: Koneksi putus -> notifikasi
window.addEventListener('offline', () => {
    console.log('[Offline CBT] Koneksi terputus. Mode Offline aktif.');
    showSyncNotification('error', 'Koneksi internet terputus! Jawaban Anda akan tersimpan di perangkat dan disinkronkan saat koneksi pulih.');
});

// ============================================================
// 3. SMART AUTO-SAVE (Online/Offline Aware)
// ============================================================

/**
 * Fungsi utama Auto-Save yang digunakan oleh halaman ujian.
 * Otomatis memilih menyimpan ke Server (jika online) atau IndexedDB (jika offline).
 */
async function smartAutoSave(examSessionId, soalId, optionId, isDoubtful = false) {
    if (navigator.onLine) {
        // ONLINE: Kirim langsung ke Redis via API
        try {
            const response = await fetch('/api/exam/answer/save', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    exam_session_id: examSessionId,
                    soal_id: soalId,
                    option_id: optionId,
                }),
            });

            if (!response.ok) throw new Error('API Error');
            return { saved_to: 'server' };

        } catch (error) {
            // Fallback ke offline jika API gagal
            await saveAnswerOffline(examSessionId, soalId, optionId, isDoubtful);
            return { saved_to: 'offline' };
        }
    } else {
        // OFFLINE: Simpan ke IndexedDB
        await saveAnswerOffline(examSessionId, soalId, optionId, isDoubtful);
        return { saved_to: 'offline' };
    }
}

// ============================================================
// 4. SERVICE WORKER REGISTRATION
// ============================================================

if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/sw.js')
            .then((reg) => console.log('[SW] Registered:', reg.scope))
            .catch((err) => console.error('[SW] Registration failed:', err));
    });
}

// Export untuk digunakan di halaman ujian
window.CBTOffline = {
    smartAutoSave,
    syncPendingAnswers,
    getPendingAnswers,
    saveAnswerOffline,
};
