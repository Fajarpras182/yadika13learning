<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$courses = \App\Models\Course::with('schoolClass')->get();
foreach ($courses as $course) {
    echo "Course ID: {$course->id}, Name: {$course->nama_mata_pelajaran}, Class ID: " . ($course->class_id ?? 'null') . "\n";
    if ($course->schoolClass) {
        echo "  - SchoolClass Name: {$course->schoolClass->name}, student_count: {$course->schoolClass->student_count}\n";
    }
    $studentsViaRel = $course->students()->count();
    echo "  - students()->count() : {$studentsViaRel}\n";
    
    // Manual query
    if ($course->class_id) {
        $manualCount = \App\Models\User::where('class_id', $course->class_id)->where('role', 'siswa')->count();
        echo "  - Manual query count : {$manualCount}\n";
    }
}

echo "\n--- Students ---\n";
$students = \App\Models\User::where('role', 'siswa')->get();
foreach ($students as $student) {
    echo "Student: {$student->name}, class_id: " . ($student->class_id ?? 'null') . ", kelas: " . json_encode($student->kelas) . "\n";
}
