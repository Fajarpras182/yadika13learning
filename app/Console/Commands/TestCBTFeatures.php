<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;

class TestCBTFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cbt:test-features';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test all newly added CBT Enterprise features';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("=== STARTING CBT ENTERPRISE FEATURES TEST ===\n");

        $passed = 0;
        $failed = 0;

        $this->info("1. MENGUJI DATABASE SCHEMA & MIGRATIONS");
        
        $tables = ['wacanas', 'ujian_answers_archives', 'ujian_results_archives', 'audit_logs'];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->line(" [OK] Tabel '{$table}' ditemukan.");
                $passed++;
            } else {
                $this->error(" [FAIL] Tabel '{$table}' TIDAK ditemukan.");
                $failed++;
            }
        }

        $columns = [
            'soal' => ['wacana_id', 'is_random_options'],
            'ujian_answers' => ['options_order', 'is_doubtful'],
            'sesi_ujians' => ['allowed_ip_ranges', 'kkm', 'allow_remedial']
        ];

        foreach ($columns as $table => $cols) {
            foreach ($cols as $col) {
                if (Schema::hasColumn($table, $col)) {
                    $this->line(" [OK] Kolom '{$col}' di tabel '{$table}' ditemukan.");
                    $passed++;
                } else {
                    $this->error(" [FAIL] Kolom '{$col}' di tabel '{$table}' TIDAK ditemukan.");
                    $failed++;
                }
            }
        }

        $this->info("\n2. MENGUJI API ROUTES");
        
        $expectedRoutes = [
            'api.exam.autosave',
            'api.exam.heartbeat',
            'api.exam.violation',
            'api.exam.doubtful',
            'api.exam.bulksync'
        ];

        foreach ($expectedRoutes as $routeName) {
            if (Route::has($routeName)) {
                $this->line(" [OK] Route '{$routeName}' ditemukan.");
                $passed++;
            } else {
                $this->error(" [FAIL] Route '{$routeName}' TIDAK ditemukan.");
                $failed++;
            }
        }

        $this->info("\n3. MENGUJI FILE FRONTEND (PWA & OFFLINE MODE)");

        $frontendFiles = [
            'public/js/exam-proctoring.js',
            'public/js/offline-cbt.js',
            'public/js/live-dashboard.js',
            'public/sw.js',
            'public/manifest.json'
        ];

        foreach ($frontendFiles as $file) {
            if (file_exists(base_path($file))) {
                $this->line(" [OK] File '{$file}' ditemukan.");
                $passed++;
            } else {
                $this->error(" [FAIL] File '{$file}' TIDAK ditemukan.");
                $failed++;
            }
        }

        $this->info("\n4. MENGUJI KELAS & MIDDLEWARE");

        $classes = [
            \App\Models\Wacana::class,
            \App\Services\Exam\OptionRandomizerService::class,
            \App\Services\Exam\ItemAnalysisService::class,
            \App\Services\Exam\ExamGradingService::class,
            \App\Http\Middleware\VerifySafeExamBrowser::class,
            \App\Http\Middleware\VerifyIpAddress::class,
            \App\Http\Middleware\PreventMultipleExamLogins::class,
            \App\Jobs\ProcessAutoRemedial::class,
            \App\Jobs\GradeExamJob::class,
        ];

        foreach ($classes as $class) {
            if (class_exists($class)) {
                $this->line(" [OK] Class '{$class}' terdaftar.");
                $passed++;
            } else {
                $this->error(" [FAIL] Class '{$class}' TIDAK terdaftar.");
                $failed++;
            }
        }

        $this->info("\n=== HASIL TEST ===");
        $this->line("Passed: <info>{$passed}</info>");
        $this->line("Failed: " . ($failed > 0 ? "<error>{$failed}</error>" : "<info>0</info>"));

        if ($failed === 0) {
            $this->info("\n✨ SEMUA FITUR CBT ENTERPRISE BERHASIL TERPASANG DAN SIAP DIGUNAKAN! ✨");
        } else {
            $this->error("\n❌ ADA BEBERAPA KOMPONEN YANG GAGAL ATAU HILANG.");
        }
    }
}
