<?php

namespace App\Jobs;

use App\Models\UjianResult;
use App\Models\SesiUjian;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAutoRemedial implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @param int $ujianResultId  ID dari ujian_results yang baru saja selesai
     */
    public function __construct(private int $ujianResultId)
    {
    }

    /**
     * Execute the job.
     * Mengecek apakah skor siswa di bawah KKM.
     * Jika ya, buat sesi ujian remedial baru secara otomatis.
     */
    public function handle(): void
    {
        $ujianResult = UjianResult::with('sesiUjian')->find($this->ujianResultId);

        if (!$ujianResult) {
            Log::warning("ProcessAutoRemedial: UjianResult ID {$this->ujianResultId} tidak ditemukan.");
            return;
        }

        $sesiUjian = $ujianResult->sesiUjian;

        if (!$sesiUjian || !$sesiUjian->allow_remedial) {
            return; // Remedial tidak diaktifkan untuk sesi ini
        }

        $kkm = $sesiUjian->kkm ?? 75.00;

        if ($ujianResult->score >= $kkm) {
            return; // Nilai memenuhi KKM, tidak perlu remedial
        }

        // Buat sesi ujian remedial baru
        // Cek apakah sudah pernah ada remedial sebelumnya untuk siswa ini
        $existingRemedial = UjianResult::where('student_id', $ujianResult->student_id)
            ->where('sesi_ujian_id', $sesiUjian->id)
            ->where('status', 'remedial')
            ->exists();

        if ($existingRemedial) {
            Log::info("ProcessAutoRemedial: Siswa {$ujianResult->student_id} sudah memiliki sesi remedial.");
            return;
        }

        // Buat record UjianResult baru sebagai remedial
        UjianResult::create([
            'sesi_ujian_id'       => $sesiUjian->id,
            'student_id'          => $ujianResult->student_id,
            'start_time'          => null, // Belum mulai
            'end_time'            => null,
            'score'               => 0,
            'total_questions'     => $ujianResult->total_questions,
            'time_taken_minutes'  => 0,
            'status'              => 'remedial', // Status khusus remedial
        ]);

        Log::info("ProcessAutoRemedial: Sesi remedial dibuat untuk Siswa {$ujianResult->student_id} pada Sesi Ujian {$sesiUjian->id}. Skor: {$ujianResult->score}, KKM: {$kkm}.");
    }
}
