<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$guru = \App\Models\User::where('role', 'guru')->first();
$assignment = \App\Models\Assignment::first();
if (!$assignment) {
    echo "No assignment found.\n";
    exit;
}

$grade = \App\Models\Grade::where('assignment_id', $assignment->id)->first();
if (!$grade) {
    echo "No grade found. Creating one...\n";
    $siswa = \App\Models\User::where('role', 'siswa')->first();
    $grade = \App\Models\Grade::create([
        'assignment_id' => $assignment->id,
        'student_id' => $siswa->id,
        'status' => 'sudah_dikumpulkan',
        'submitted_at' => now(),
        'jawaban_text' => 'Test answer'
    ]);
}

echo "Testing bulk update for Grade ID: {$grade->id}\n";
echo "Assignment Bobot Nilai: {$assignment->bobot_nilai}\n";

$request = \Illuminate\Http\Request::create("/guru/grades/bulk/{$assignment->id}", 'PATCH', [
    'nilai' => [
        $grade->id => '95'
    ],
    'feedback' => [
        $grade->id => 'Bagus sekali!'
    ]
]);

// Authenticate as guru
auth()->login($guru);

$controller = new \App\Http\Controllers\GuruController();
try {
    $response = $controller->bulkUpdateGrades($request, $assignment->id);
    echo "Response status: " . ($response->getStatusCode() ?? 'unknown') . "\n";
    
    // Check if grade was updated
    $grade->refresh();
    echo "Updated Grade Nilai: {$grade->nilai}\n";
    echo "Updated Grade Feedback: {$grade->feedback}\n";
} catch (\Illuminate\Validation\ValidationException $e) {
    echo "Validation failed!\n";
    print_r($e->errors());
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
