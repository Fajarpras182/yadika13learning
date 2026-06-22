<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Course;
use App\Models\SchoolClass;
use App\Models\Schedule;

// Find a guru
$guru = User::where('role', 'guru')->first();
if (!$guru) {
    echo "No guru found\n";
    exit;
}

echo "Guru: {$guru->name} (ID: {$guru->id})\n";

$courseIds = Course::where('guru_id', $guru->id)->pluck('id');
echo "Course IDs: " . implode(', ', $courseIds->toArray()) . "\n";

$classIdsFromCourses = Course::where('guru_id', $guru->id)->whereNotNull('class_id')->pluck('class_id');
echo "Class IDs from courses: " . implode(', ', $classIdsFromCourses->toArray()) . "\n";

$classIdsFromSchedules = Schedule::whereIn('course_id', $courseIds)->pluck('class_id');
echo "Class IDs from schedules: " . implode(', ', $classIdsFromSchedules->toArray()) . "\n";

$allClassIds = $classIdsFromCourses->concat($classIdsFromSchedules)->unique()->filter();
echo "All unique Class IDs: " . implode(', ', $allClassIds->toArray()) . "\n";

$classes = SchoolClass::whereIn('id', $allClassIds)->get();
echo "Classes count: " . $classes->count() . "\n";
foreach ($classes as $class) {
    echo "- Class: {$class->name} (ID: {$class->id})\n";
}
