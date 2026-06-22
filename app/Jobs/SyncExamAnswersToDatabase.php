<?php

namespace App\Jobs;

use App\Models\UjianAnswer;
use App\Models\UjianResult;
use App\Services\Exam\ExamCacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncExamAnswersToDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // Job ini akan dijalankan tanpa parameter spesifik, 
        // melainkan akan menscan seluruh sesi yang aktif.
        // Dalam implementasi nyata tingkat lanjut, bisa dipassing array of session_ids.
    }

    /**
     * Execute the job.
     */
    public function handle(ExamCacheService $cacheService): void
    {
        // 1. Dapatkan daftar ujian yang masih aktif
        // (Misalnya: status 'in_progress' atau end_time belum berlalu terlalu lama)
        // Sebagai contoh implementasi, kita ambil sesi yang statusnya masih 'in_progress'.
        // Catatan: Jika tidak ada field 'status', kita asumsikan mencari semua result yang belum selesai
        
        // Asumsi skema: ujian_results memiliki end_time atau disubmit_at.
        // Kita ambil semua ID sesi yang masih butuh sinkronisasi.
        // Untuk saat ini (prototipe), kita ambil seluruh session_id yang memiliki cache di Redis.
        
        $sessionIds = $this->getActiveSessionIdsFromRedis();

        foreach ($sessionIds as $sessionId) {
            $this->syncSession($sessionId, $cacheService);
        }
    }

    /**
     * Sinkronisasi per sesi
     */
    private function syncSession(int $sessionId, ExamCacheService $cacheService): void
    {
        $state = $cacheService->getSessionState($sessionId);
        
        if (empty($state)) {
            return;
        }

        $upsertData = [];
        
        foreach ($state as $key => $value) {
            // Abaikan last_active, kita hanya ambil ans:{soal_id}
            if (str_starts_with($key, 'ans:')) {
                $soalId = (int) str_replace('ans:', '', $key);
                $optionId = $value; // Bisa berupa string ID

                $upsertData[] = [
                    'ujian_result_id' => $sessionId,
                    'question_id'     => $soalId,
                    'selected_answer' => (string) $optionId,
                    // Karena auto-save, is_correct dan points bisa dihitung terpisah 
                    // nanti saat final submit (Auto-Grade Service).
                    'is_correct'      => false, 
                    'points'          => 0,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        if (!empty($upsertData)) {
            try {
                // Gunakan UPSERT untuk efisiensi massal.
                // Jika kombinasi (ujian_result_id, question_id) unik, lakukan update.
                UjianAnswer::upsert(
                    $upsertData,
                    ['ujian_result_id', 'question_id'], // unique columns (pastikan ada unique constraint di DB)
                    ['selected_answer', 'updated_at']   // update columns
                );
            } catch (\Exception $e) {
                Log::error("Gagal sinkronisasi Redis ke MySQL untuk Session ID: {$sessionId}. Error: " . $e->getMessage());
            }
        }
    }

    /**
     * Utility untuk menemukan semua key session di Redis (menggunakan SCAN untuk performa, bukan KEYS)
     */
    private function getActiveSessionIdsFromRedis(): array
    {
        $prefix = 'cbt:session:*:state';
        // Implementasi sederhana menggunakan facade Redis
        // Untuk production, sangat disarankan menggunakan SCAN.
        $keys = \Illuminate\Support\Facades\Redis::keys($prefix);
        
        $sessionIds = [];
        foreach ($keys as $fullKey) {
            // $fullKey biasanya "laravel_database_cbt:session:1:state" jika pakai driver predis default prefix
            // Kita extract angka di tengah
            if (preg_match('/session:(\d+):state/', $fullKey, $matches)) {
                $sessionIds[] = (int) $matches[1];
            }
        }

        return array_unique($sessionIds);
    }
}
