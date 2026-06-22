<?php

namespace App\Services\Exam;

use Illuminate\Support\Facades\Redis;
use App\Models\BankSoal;

class ExamCacheService
{
    private const CACHE_PREFIX = 'cbt:';

    /**
     * Memuat daftar soal ke Redis (Cache Aside)
     *
     * @param int $bankSoalId
     * @return array
     */
    public function getBankSoal(int $bankSoalId): array
    {
        $key = self::CACHE_PREFIX . "bank_soal:{$bankSoalId}:questions";
        
        $cached = Redis::get($key);
        if ($cached) {
            return json_decode($cached, true);
        }

        // Fallback ke MySQL dengan Eager Loading
        // Menggunakan with('soals.options') untuk menarik seluruh relasi.
        // Jika model Soal menggunakan nama relasi yang berbeda (misal 'questions'), mohon sesuaikan.
        $bankSoal = BankSoal::with(['soals.options'])->findOrFail($bankSoalId);
        
        $data = $bankSoal->soals->toArray();
        
        // Simpan ke Redis, Expire dalam 24 Jam (86400 detik)
        Redis::setex($key, 86400, json_encode($data));
        
        return $data;
    }

    /**
     * Menyimpan jawaban sementara siswa ke Redis Hash dengan sangat cepat (O(1))
     *
     * @param int $examSessionId
     * @param int $soalId
     * @param int $optionId
     * @return void
     */
    public function saveTemporaryAnswer(int $examSessionId, int $soalId, int|string $optionId): void
    {
        $key = self::CACHE_PREFIX . "session:{$examSessionId}:state";
        
        // HSET untuk meng-update field spesifik tanpa me-replace jawaban soal lain
        Redis::hset($key, "ans:{$soalId}", (string) $optionId);
        Redis::hset($key, "last_active", now()->timestamp);
        
        // Perbarui masa hidup sesi cache agar tidak cepat terhapus (4 Jam)
        Redis::expire($key, 14400); 
    }

    /**
     * Mengambil seluruh jawaban dari cache untuk sinkronisasi ke DB
     *
     * @param int $examSessionId
     * @return array
     */
    public function getSessionState(int $examSessionId): array
    {
        $key = self::CACHE_PREFIX . "session:{$examSessionId}:state";
        return Redis::hgetall($key);
    }
}
