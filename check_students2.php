<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$courses = \App\Models\Course::with('schedules')->get();
foreach ($courses as $course) {
    echo "Course ID: {$course->id}, Name: {$course->nama_mata_pelajaran}, Class ID: " . ($course->class_id ?? 'null') . "\n";
    echo "  - student_count attribute: {$course->student_count}\n";
    echo "  - students attribute count: " . $course->students->count() . "\n";
    echo "  - schedules count: " . $course->schedules->count() . "\n";
}

$siswa1 = \App\Models\User::where('role', 'siswa')->first();
if ($siswa1) {
    echo "\nSiswa: {$siswa1->name} (Class ID: {$siswa1->class_id})\n";
    
    $courseIds = \App\Models\Course::where('class_id', $siswa1->class_id)
        ->orWhereHas('schedules', function($query) use ($siswa1) {
            $query->where('class_id', $siswa1->class_id);
        })->pluck('id');
        
    echo "Enrolled Courses count: " . $courseIds->count() . "\n";
    foreach (\App\Models\Course::whereIn('id', $courseIds)->get() as $c) {
        echo "  - {$c->nama_mata_pelajaran}\n";
    }
}
