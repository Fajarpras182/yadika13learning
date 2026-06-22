/**
 * Enterprise-Level CBT: Service Worker
 * Meng-cache halaman ujian agar tetap bisa ditampilkan saat offline.
 */

const CACHE_NAME = 'cbt-cache-v1';
const URLS_TO_CACHE = [
    '/',
    '/js/exam-proctoring.js',
    '/js/offline-cbt.js',
    '/css/app.css',
    '/manifest.json',
];

// INSTALL: Meng-cache aset-aset statis
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[SW] Caching app shell');
            return cache.addAll(URLS_TO_CACHE);
        })
    );
    self.skipWaiting();
});

// ACTIVATE: Menghapus cache lama jika versi berubah
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.filter(name => name !== CACHE_NAME)
                          .map(name => caches.delete(name))
            );
        })
    );
    self.clients.claim();
});

// FETCH: Network-First Strategy
// Coba ambil dari jaringan dulu. Jika gagal (offline), ambil dari cache.
self.addEventListener('fetch', (event) => {
    // Hanya intercept request GET
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request)
            .then((response) => {
                // Simpan respons terbaru ke cache
                const responseClone = response.clone();
                caches.open(CACHE_NAME).then((cache) => {
                    cache.put(event.request, responseClone);
                });
                return response;
            })
            .catch(() => {
                // Offline: ambil dari cache
                return caches.match(event.request).then((response) => {
                    return response || new Response(
                        '<h1>Anda sedang Offline</h1><p>Jawaban Anda tetap tersimpan di perangkat dan akan disinkronkan saat koneksi pulih.</p>',
                        { headers: { 'Content-Type': 'text/html' } }
                    );
                });
            })
    );
});
