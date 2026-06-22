<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\SchoolClass;

// Find a guru
$guru = User::where('role', 'guru')->first();
if (!$guru) {
    echo "No guru found\n";
    exit;
}

echo "Guru: {$guru->name} (ID: {$guru->id})\n";

$courses = Course::where('guru_id', $guru->id)->with('schoolClass')->get();
echo "Courses count: " . $courses->count() . "\n";

foreach ($courses as $course) {
    echo "- Course: {$course->nama_mata_pelajaran} (Class ID: {$course->class_id}, Class Name: " . ($course->schoolClass->name ?? 'N/A') . ")\n";
}

$classes = SchoolClass::whereHas('courses', function($q) use ($guru) {
    $q->where('guru_id', $guru->id);
})->get();

echo "Classes count: " . $classes->count() . "\n";
foreach ($classes as $class) {
    echo "- Class: {$class->name} (ID: {$class->id})\n";
}
