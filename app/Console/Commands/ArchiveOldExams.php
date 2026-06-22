<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveOldExams extends Command
{
    /**
     * The name and signature of the console command.
     * Usage: php artisan cbt:archive-exams --year=2025
     */
    protected $signature = 'cbt:archive-exams 
                            {--year= : Tahun data yang akan diarsipkan (misal: 2025)}
                            {--dry-run : Tampilkan jumlah data tanpa benar-benar memindahkan}';

    protected $description = 'Memindahkan data ujian (results & answers) dari tahun tertentu ke tabel arsip untuk menjaga performa database utama.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $year = $this->option('year');
        $dryRun = $this->option('dry-run');

        if (!$year) {
            $this->error('Parameter --year wajib diisi. Contoh: php artisan cbt:archive-exams --year=2025');
            return Command::FAILURE;
        }

        $this->info("=== CBT Archive Tool ===");
        $this->info("Tahun target arsip: {$year}");

        // 1. Hitung data yang akan diarsipkan
        $resultsCount = DB::table('ujian_results')
            ->whereYear('created_at', $year)
            ->count();

        $answersCount = DB::table('ujian_answers')
            ->whereIn('ujian_result_id', function ($query) use ($year) {
                $query->select('id')
                      ->from('ujian_results')
                      ->whereYear('created_at', $year);
            })
            ->count();

        $this->info("Data ditemukan:");
        $this->table(
            ['Tabel', 'Jumlah Baris'],
            [
                ['ujian_results', number_format($resultsCount)],
                ['ujian_answers', number_format($answersCount)],
            ]
        );

        if ($resultsCount === 0) {
            $this->warn("Tidak ada data untuk tahun {$year}. Proses dibatalkan.");
            return Command::SUCCESS;
        }

        if ($dryRun) {
            $this->warn("[DRY RUN] Tidak ada data yang dipindahkan. Jalankan tanpa --dry-run untuk eksekusi.");
            return Command::SUCCESS;
        }

        if (!$this->confirm("Apakah Anda yakin ingin memindahkan {$resultsCount} results dan {$answersCount} answers ke tabel arsip?")) {
            $this->info('Proses dibatalkan oleh pengguna.');
            return Command::SUCCESS;
        }

        $this->info('Memulai proses archiving...');
        $bar = $this->output->createProgressBar(3);

        try {
            DB::transaction(function () use ($year, $bar) {

                // STEP 1: Pindahkan ujian_answers ke arsip
                $bar->advance();
                $this->newLine();
                $this->info('  [1/3] Memindahkan ujian_answers ke arsip...');

                DB::statement("
                    INSERT INTO ujian_answers_archives 
                        (original_id, ujian_result_id, question_id, selected_answer, 
                         options_order, is_doubtful, is_correct, points, 
                         created_at, updated_at, archived_at)
                    SELECT 
                        a.id, a.ujian_result_id, a.question_id, a.selected_answer,
                        a.options_order, a.is_doubtful, a.is_correct, a.points,
                        a.created_at, a.updated_at, NOW()
                    FROM ujian_answers a
                    INNER JOIN ujian_results r ON a.ujian_result_id = r.id
                    WHERE YEAR(r.created_at) = ?
                ", [$year]);

                // STEP 2: Pindahkan ujian_results ke arsip
                $bar->advance();
                $this->newLine();
                $this->info('  [2/3] Memindahkan ujian_results ke arsip...');

                DB::statement("
                    INSERT INTO ujian_results_archives
                        (original_id, sesi_ujian_id, student_id, start_time, end_time,
                         score, total_questions, time_taken_minutes, status,
                         created_at, updated_at, archived_at)
                    SELECT
                        id, sesi_ujian_id, student_id, start_time, end_time,
                        score, total_questions, time_taken_minutes, status,
                        created_at, updated_at, NOW()
                    FROM ujian_results
                    WHERE YEAR(created_at) = ?
                ", [$year]);

                // STEP 3: Hapus data asli dari tabel utama
                $bar->advance();
                $this->newLine();
                $this->info('  [3/3] Menghapus data asli dari tabel utama...');

                // Hapus answers dulu (child table)
                DB::table('ujian_answers')
                    ->whereIn('ujian_result_id', function ($query) use ($year) {
                        $query->select('id')
                              ->from('ujian_results')
                              ->whereYear('created_at', $year);
                    })
                    ->delete();

                // Lalu hapus results (parent table)
                DB::table('ujian_results')
                    ->whereYear('created_at', $year)
                    ->delete();
            });

            $bar->finish();
            $this->newLine(2);
            $this->info("✅ Archiving selesai! Data tahun {$year} berhasil dipindahkan ke tabel arsip.");
            Log::info("CBT Archive: Data tahun {$year} berhasil diarsipkan. Results: {$resultsCount}, Answers: {$answersCount}.");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->newLine(2);
            $this->error("❌ Archiving GAGAL: " . $e->getMessage());
            Log::error("CBT Archive FAILED: " . $e->getMessage());

            return Command::FAILURE;
        }
    }
}
