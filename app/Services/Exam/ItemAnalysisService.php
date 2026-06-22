<?php

namespace App\Services\Exam;

use App\Models\Question;
use App\Models\UjianAnswer;
use Illuminate\Support\Facades\DB;

class ItemAnalysisService
{
    /**
     * Menghitung Indeks Kesukaran (Difficulty Index / p-value)
     * Rumus: Jumlah peserta yang menjawab benar / Total peserta
     * Rentang: 0.00 - 1.00 (Makin mendekati 1 makin mudah, < 0.3 sulit)
     */
    public function calculateDifficultyIndex(int $questionId): float
    {
        $stats = UjianAnswer::where('question_id', $questionId)
            ->selectRaw('count(*) as total_answers')
            ->selectRaw('sum(case when is_correct = 1 then 1 else 0 end) as correct_answers')
            ->first();

        if ($stats->total_answers == 0) return 0.0;

        return round($stats->correct_answers / $stats->total_answers, 2);
    }

    /**
     * Menghitung Daya Beda (Discrimination Index)
     * Rumus Sederhana (Kelompok Atas - Kelompok Bawah)
     * Membandingkan jawaban benar dari 27% siswa top vs 27% siswa bottom
     */
    public function calculateDiscriminationIndex(int $bankSoalId, int $questionId): float
    {
        // 1. Ambil semua UjianResult untuk bank soal ini dan urutkan berdasarkan skor descending
        // Note: Asumsi ujian_results terhubung dengan sesi_ujian, dan sesi_ujian terhubung ke bank_soal
        // Implementasi ini disederhanakan.
        
        $results = DB::table('ujian_results')
            ->join('sesi_ujians', 'ujian_results.sesi_ujian_id', '=', 'sesi_ujians.id')
            ->where('sesi_ujians.bank_soal_id', $bankSoalId)
            ->orderByDesc('ujian_results.score')
            ->pluck('ujian_results.id');

        $totalStudents = $results->count();
        if ($totalStudents < 10) {
            return 0.0; // Tidak cukup data statistik (minimal 10 peserta disarankan)
        }

        $groupSize = max(1, floor($totalStudents * 0.27)); // Ambil 27% grup atas dan bawah
        
        $topGroupIds = $results->take($groupSize)->toArray();
        $bottomGroupIds = $results->reverse()->take($groupSize)->toArray();

        $topCorrect = UjianAnswer::whereIn('ujian_result_id', $topGroupIds)
                                 ->where('question_id', $questionId)
                                 ->where('is_correct', 1)->count();
                                 
        $bottomCorrect = UjianAnswer::whereIn('ujian_result_id', $bottomGroupIds)
                                    ->where('question_id', $questionId)
                                    ->where('is_correct', 1)->count();

        // Rumus DI = (Ru - Rl) / N_group
        // Ru = Correct in upper group
        // Rl = Correct in lower group
        $di = ($topCorrect - $bottomCorrect) / $groupSize;

        return round($di, 2);
    }
}
