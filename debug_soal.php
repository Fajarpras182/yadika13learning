<?php
// Verifikasi logika relasi Ujian::questions() baru dan virtual inject

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Ujian;
use App\Models\Question;

echo "=== VERIFIKASI LOGIKA BARU ===\n\n";

$ujians = Ujian::with('course')->get();
foreach ($ujians as $ujian) {
    echo "Ujian [{$ujian->id}]: {$ujian->judul}\n";
    echo "  course_id: {$ujian->course_id} ({$ujian->course->nama_mata_pelajaran})\n";
    
    // Test relasi baru: questions() via course_id
    $count = $ujian->questions()->count();
    echo "  \$ujian->questions()->count() = {$count}  ← (seharusnya > 0)\n";
    
    // Test virtual inject simulasi
    $questions = Question::where('course_id', $ujian->course_id)->get();
    $questions->each(fn($q) => $q->ujian_id = $ujian->id);
    
    echo "  Soal setelah virtual inject:\n";
    foreach ($questions as $q) {
        $pertanyaan = substr(strip_tags($q->pertanyaan ?? ''), 0, 40);
        echo "    - ID:{$q->id} | ujian_id:{$q->ujian_id} | {$pertanyaan}\n";
        echo "      Badge View: " . ((int)$q->ujian_id === (int)$ujian->id ? '"Di ujian ini" ✅' : '"Bank" ❌') . "\n";
    }
    echo "\n";
}

echo "=== SELESAI ===\n";
