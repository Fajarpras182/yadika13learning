<?php

namespace App\Services\Exam;

use App\Models\UjianResult;
use App\Models\UjianAnswer;
use Illuminate\Support\Facades\DB;

class ExamGradingService
{
    /**
     * Menghitung nilai akhir ujian untuk sebuah session.
     */
    public function gradeSession(int $examSessionId): void
    {
        DB::transaction(function () use ($examSessionId) {
            $ujianResult = UjianResult::with('answers.question.options')->findOrFail($examSessionId);
            
            $totalScore = 0;
            $correctAnswers = 0;

            foreach ($ujianResult->answers as $answer) {
                // Implementasi sederhana untuk Pilihan Ganda
                $isCorrect = false;
                $points = 0;
                
                // Cari opsi yang sesuai
                $selectedOption = $answer->question->options->where('id', $answer->selected_answer)->first();
                
                if ($selectedOption && $selectedOption->is_correct) {
                    $isCorrect = true;
                    // Asumsi: setiap soal memiliki bobot default 1
                    $points = 1; 
                    $correctAnswers++;
                }

                $answer->update([
                    'is_correct' => $isCorrect,
                    'points' => $points,
                ]);
                
                $totalScore += $points;
            }

            // Normalisasi nilai ke rentang 0-100 (jika diinginkan)
            $totalQuestions = $ujianResult->answers->count();
            $finalScore = $totalQuestions > 0 ? ($correctAnswers / $totalQuestions) * 100 : 0;

            $ujianResult->update([
                'score' => $finalScore,
                // Status ujian bisa diupdate di sini, misal: 'graded'
            ]);
        });
    }
}
