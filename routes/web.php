<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuruController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\ClassController as AdminClassController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
| 
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home route - Landing page for guests, redirect authenticated users
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'guru':
                return redirect()->route('guru.dashboard');
            case 'siswa':
                return redirect()->route('siswa.dashboard');
            default:
                return redirect()->route('logout');
        }
    }
    return view('home');
})->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Data Master
    Route::get('/data-master', [AdminController::class, 'dataMaster'])->name('data-master');

    // Profile
    Route::get('/profile', [AdminController::class, 'profile'])->name('profile');
    Route::put('/profile', [AdminController::class, 'updateProfile'])->name('profile.update');

    // User management
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::patch('/users/{id}/activate', [AdminController::class, 'activateUser'])->name('users.activate');
    Route::patch('/users/{id}/deactivate', [AdminController::class, 'deactivateUser'])->name('users.deactivate');
    Route::get('/users-export-excel', [AdminController::class, 'exportUsersExcel'])->name('users.export.excel');
    Route::get('/users-export-csv', [AdminController::class, 'exportUsersCsv'])->name('users.export.csv');
    Route::get('/users-export-pdf', [AdminController::class, 'exportUsersPdf'])->name('users.export.pdf');
    Route::get('/users-export-word', [AdminController::class, 'exportUsersWord'])->name('users.export.word');
    Route::post('/users-import', [AdminController::class, 'importUsers'])->name('users.import');

    // Teacher management
    Route::get('/teachers', [TeacherController::class, 'index'])->name('teachers.index');
    Route::get('/teachers/create', [TeacherController::class, 'create'])->name('teachers.create');
    Route::post('/teachers', [TeacherController::class, 'store'])->name('teachers.store');
    Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])->name('teachers.edit');
    Route::patch('/teachers/{teacher}', [TeacherController::class, 'update'])->name('teachers.update');
    Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])->name('teachers.destroy');
    Route::patch('/teachers/{id}/activate', [TeacherController::class, 'activate'])->name('teachers.activate');
    Route::patch('/teachers/{id}/deactivate', [TeacherController::class, 'deactivate'])->name('teachers.deactivate');
    Route::get('/teachers-export-excel', [TeacherController::class, 'exportExcel'])->name('teachers.export.excel');
    Route::get('/teachers-export-csv', [TeacherController::class, 'exportCsv'])->name('teachers.export.csv');
    Route::get('/teachers-export-pdf', [TeacherController::class, 'exportPdf'])->name('teachers.export.pdf');
    Route::get('/teachers-export-word', [TeacherController::class, 'exportWord'])->name('teachers.export.word');
    Route::post('/teachers-import', [TeacherController::class, 'import'])->name('teachers.import');

    // Student management
    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
    Route::patch('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::patch('/students/{id}/activate', [StudentController::class, 'activate'])->name('students.activate');
    Route::patch('/students/{id}/deactivate', [StudentController::class, 'deactivate'])->name('students.deactivate');
    Route::get('/students-export-excel', [StudentController::class, 'exportExcel'])->name('students.export.excel');
    Route::get('/students-export-csv', [StudentController::class, 'exportCsv'])->name('students.export.csv');
    Route::get('/students-export-pdf', [StudentController::class, 'exportPdf'])->name('students.export.pdf');
    Route::get('/students-export-word', [StudentController::class, 'exportWord'])->name('students.export.word');
    Route::post('/students-import', [StudentController::class, 'import'])->name('students.import');

    // Course management
    Route::get('/courses', [AdminController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [AdminController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [AdminController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}/edit', [AdminController::class, 'editCourse'])->name('courses.edit');
    Route::patch('/courses/{course}', [AdminController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [AdminController::class, 'destroyCourse'])->name('courses.destroy');
    Route::get('/courses-export-excel', [AdminController::class, 'exportCoursesExcel'])->name('courses.export.excel');
    Route::get('/courses-export-csv', [AdminController::class, 'exportCoursesCsv'])->name('courses.export.csv');
    Route::get('/courses-export-pdf', [AdminController::class, 'exportCoursesPdf'])->name('courses.export.pdf');
    Route::get('/courses-export-word', [AdminController::class, 'exportCoursesWord'])->name('courses.export.word');
    Route::post('/courses-import', [AdminController::class, 'importCourses'])->name('courses.import');

    // Majors (Jurusan)
    Route::resource('majors', MajorController::class);
    Route::get('majors-export-excel', [MajorController::class, 'exportExcel'])->name('majors.export.excel');
    Route::get('majors-export-csv', [MajorController::class, 'exportCsv'])->name('majors.export.csv');
    Route::get('majors-export-pdf', [MajorController::class, 'exportPdf'])->name('majors.export.pdf');
    Route::get('majors-export-word', [MajorController::class, 'exportWord'])->name('majors.export.word');
    Route::post('majors-import', [MajorController::class, 'import'])->name('majors.import');

    // Classes (Kelas)
    Route::resource('classes', AdminClassController::class);
    Route::get('classes-export-excel', [AdminClassController::class, 'exportExcel'])->name('classes.export.excel');
    Route::get('classes-export-csv', [AdminClassController::class, 'exportCsv'])->name('classes.export.csv');
    Route::get('classes-export-pdf', [AdminClassController::class, 'exportPdf'])->name('classes.export.pdf');
    Route::get('classes-export-word', [AdminClassController::class, 'exportWord'])->name('classes.export.word');
    Route::post('classes-import', [AdminClassController::class, 'import'])->name('classes.import');

    // Schedules (Jadwal)
    Route::resource('schedules', ScheduleController::class);
    Route::get('schedules-export-excel', [ScheduleController::class, 'exportExcel'])->name('schedules.export.excel');
    Route::get('schedules-export-csv', [ScheduleController::class, 'exportCsv'])->name('schedules.export.csv');
    Route::get('schedules-export-pdf', [ScheduleController::class, 'exportPdf'])->name('schedules.export.pdf');
    Route::get('schedules-export-word', [ScheduleController::class, 'exportWord'])->name('schedules.export.word');
    Route::post('schedules-import', [ScheduleController::class, 'import'])->name('schedules.import');

    // Course enrollment
    Route::post('/courses/{course}/enroll-students', [AdminController::class, 'enrollStudents'])->name('courses.enroll-students');
    Route::post('/courses/{course}/enroll-student', [AdminController::class, 'enrollStudent'])->name('courses.enroll-student');
    Route::delete('/courses/{course}/unenroll-student/{student}', [AdminController::class, 'unenrollStudent'])->name('courses.unenroll-student');

    // Ujian (Exam) Management
    Route::get('/ujian', [AdminController::class, 'ujian'])->name('ujian');
    Route::get('/ujian/create', [AdminController::class, 'createUjian'])->name('ujian.create');
    Route::get('/ujian/{ujian}', [AdminController::class, 'showUjian'])->name('ujian.show');
    Route::post('/ujian', [AdminController::class, 'storeUjian'])->name('ujian.store');
    Route::get('/ujian/{ujian}/edit', [AdminController::class, 'editUjian'])->name('ujian.edit');
    Route::put('/ujian/{ujian}', [AdminController::class, 'updateUjian'])->name('ujian.update');
    Route::delete('/ujian/{ujian}', [AdminController::class, 'deleteUjian'])->name('ujian.delete');
    Route::get('/ujian/{ujian}/add-questions', [AdminController::class, 'addQuestionsToUjian'])->name('ujian.add-questions');
    Route::post('/ujian/{ujian}/add-questions', [AdminController::class, 'storeQuestionsToUjian'])->name('ujian.store-questions');
    Route::delete('/ujian/{ujian}/remove-question/{question}', [AdminController::class, 'removeQuestionFromUjian'])->name('ujian.remove-question');
    Route::post('/ujian/upload-image', [AdminController::class, 'uploadSoalImage'])->name('ujian.upload-image');

    // Sesi Ujian Management
    Route::get('/sesi-ujian', [AdminController::class, 'sesiUjian'])->name('sesi-ujian');
    Route::get('/sesi-ujian/create', [AdminController::class, 'createSesiUjian'])->name('sesi-ujian.create');
    Route::post('/sesi-ujian', [AdminController::class, 'storeSesiUjian'])->name('sesi-ujian.store');
    Route::get('/sesi-ujian/{sesi}', [AdminController::class, 'showSesiUjian'])->name('sesi-ujian.show');
    Route::get('/sesi-ujian/{sesi}/edit', [AdminController::class, 'editSesiUjian'])->name('sesi-ujian.edit');
    Route::put('/sesi-ujian/{sesi}', [AdminController::class, 'updateSesiUjian'])->name('sesi-ujian.update');
    Route::delete('/sesi-ujian/{sesi}', [AdminController::class, 'deleteSesiUjian'])->name('sesi-ujian.destroy');

    // Sesi Ujian Student Management
    Route::post('/sesi-ujian/{sesi}/students', [AdminController::class, 'storeSesiUjianStudent'])->name('sesi-ujian.student.store');
    Route::post('/sesi-ujian/{sesi}/students/bulk', [AdminController::class, 'bulkAssignSesiUjianStudents'])->name('sesi-ujian.students.bulk');
    Route::delete('/sesi-ujian/{sesi}/students/{studentId}', [AdminController::class, 'destroySesiUjianStudent'])->name('sesi-ujian.student.destroy');

    // Get courses by guru (for AJAX)
    Route::get('/courses-by-guru/{guru}', [AdminController::class, 'getCoursesByGuru']);

    // Bank Soal Management (Admin)
    Route::get('/bank-soal', [AdminController::class, 'bankSoal'])->name('bank-soal');
    Route::get('/bank-soal/create', [AdminController::class, 'createBankSoal'])->name('bank-soal.create');
    Route::post('/bank-soal', [AdminController::class, 'storeBankSoal'])->name('bank-soal.store');
    Route::get('/bank-soal/{question}', [AdminController::class, 'showBankSoal'])->name('bank-soal.show');
    Route::get('/bank-soal/{question}/edit', [AdminController::class, 'editBankSoal'])->name('bank-soal.edit');
    Route::put('/bank-soal/{question}', [AdminController::class, 'updateBankSoal'])->name('bank-soal.update');
    Route::delete('/bank-soal/{question}', [AdminController::class, 'deleteBankSoal'])->name('bank-soal.delete');
    Route::post('/bank-soal/upload', [AdminController::class, 'uploadBankSoal'])->name('bank-soal.upload');

    // Image upload for bank-soal editor
    Route::post('/bank-soal/upload-image', [AdminController::class, 'uploadBankSoalImage'])->name('bank-soal.upload-image');

    // Background settings
    Route::resource('backgrounds', \App\Http\Controllers\Admin\BackgroundController::class);
});

// Guru routes
Route::middleware(['auth', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');

    // Profile management
    Route::get('/profile', [GuruController::class, 'profile'])->name('profile');
    Route::patch('/profile', [GuruController::class, 'updateProfile'])->name('profile.update');

    // Messages
    Route::get('/messages', [GuruController::class, 'forum'])->name('messages');
    Route::post('/messages/send', [GuruController::class, 'sendMessage'])->name('messages.send');
    Route::patch('/messages/{messageId}/read', [GuruController::class, 'markMessageAsRead'])->name('messages.read');
    Route::get('/messages/{messageId}/edit', [GuruController::class, 'editMessage'])->name('messages.edit');
    Route::patch('/messages/{messageId}', [GuruController::class, 'updateMessage'])->name('messages.update');
    Route::delete('/messages/{messageId}', [GuruController::class, 'deleteMessage'])->name('messages.destroy');

    // Reports
    Route::get('/reports', [GuruController::class, 'reports'])->name('reports');
    Route::get('/rekap-nilai-tugas', [GuruController::class, 'rekaptugaspdf'])->name('rekap-nilai-tugas');
    Route::get('/attendance-reports', [GuruController::class, 'attendanceReports'])->name('attendance-reports');
    Route::get('/reports/export-attendance-pdf', [GuruController::class, 'exportAttendancePdf'])->name('reports.export-attendance-pdf');

    Route::get('/reports/export-pdf/{courseId?}', [GuruController::class, 'exportGradesPdf'])->name('reports.export-pdf');
    Route::get('/reports/export-excel/{courseId?}', [GuruController::class, 'exportGradesExcel'])->name('reports.export-excel');
    Route::get('/reports/export-word/{courseId?}', [GuruController::class, 'exportGradesWord'])->name('reports.export-word');

    // Mata Pelajaran (Subjects) - Course management
    Route::get('/subjects', [GuruController::class, 'courses'])->name('subjects');
    Route::get('/courses', [GuruController::class, 'courses'])->name('courses');
    Route::get('/courses/create', [GuruController::class, 'createCourse'])->name('courses.create');
    Route::post('/courses', [GuruController::class, 'storeCourse'])->name('courses.store');
    Route::get('/courses/{course}/edit', [GuruController::class, 'editCourse'])->name('courses.edit');
    Route::patch('/courses/{course}', [GuruController::class, 'updateCourse'])->name('courses.update');
    Route::delete('/courses/{course}', [GuruController::class, 'destroyCourse'])->name('courses.destroy');

    // Tugas Siswa (Student Assignments)
    Route::get('/student-assignments', [GuruController::class, 'allAssignments'])->name('student-assignments');

    // Lesson management
    Route::get('/courses/{courseId}/lessons', [GuruController::class, 'lessons'])->name('lessons');
    Route::get('/courses/{courseId}/lessons/create', [GuruController::class, 'createLesson'])->name('lessons.create');
    Route::post('/courses/{courseId}/lessons', [GuruController::class, 'storeLesson'])->name('lessons.store');
    Route::get('/courses/{courseId}/lessons/{lessonId}/edit', [GuruController::class, 'editLesson'])->name('lessons.edit');
    Route::patch('/courses/{courseId}/lessons/{lessonId}', [GuruController::class, 'updateLesson'])->name('lessons.update');
    Route::delete('/courses/{courseId}/lessons/{lessonId}', [GuruController::class, 'destroyLesson'])->name('lessons.destroy');

    // Assignment management
    Route::get('/courses/{courseId}/assignments', [GuruController::class, 'assignments'])->name('assignments');
    Route::get('/courses/{courseId}/assignments/create', [GuruController::class, 'createAssignment'])->name('assignments.create');
    Route::post('/courses/{courseId}/assignments', [GuruController::class, 'storeAssignment'])->name('assignments.store');
    Route::get('/assignments/{assignmentId}/status', [GuruController::class, 'assignmentStatus'])->name('assignments.status');
    Route::get('/assignments/{assignmentId}/grade', [GuruController::class, 'gradeAssignment'])->name('assignments.grade');
    Route::patch('/grades/{gradeId}', [GuruController::class, 'updateGrade'])->name('grades.update');
    Route::patch('/grades/bulk/{assignmentId}', [GuruController::class, 'bulkUpdateGrades'])->name('grades.bulk.update');
    Route::delete('/grades/{gradeId}', [GuruController::class, 'destroyGrade'])->name('grades.destroy');
    Route::get('/courses/{courseId}/assignments/{assignmentId}/edit', [GuruController::class, 'editAssignment'])->name('assignments.edit');
    Route::patch('/courses/{courseId}/assignments/{assignmentId}', [GuruController::class, 'updateAssignment'])->name('assignments.update');
    Route::delete('/courses/{courseId}/assignments/{assignmentId}', [GuruController::class, 'destroyAssignment'])->name('assignments.destroy');

    // Attendance management
    Route::get('/courses/{courseId}/attendances', [GuruController::class, 'attendances'])->name('attendances');
    Route::get('/courses/{courseId}/attendances/create', [GuruController::class, 'createAttendance'])->name('attendances.create');
    Route::post('/courses/{courseId}/attendances', [GuruController::class, 'storeAttendance'])->name('attendances.store');
    Route::get('/courses/{courseId}/attendances/{attendanceId}/edit', [GuruController::class, 'editAttendance'])->name('attendances.edit');
    Route::patch('/courses/{courseId}/attendances/{attendanceId}', [GuruController::class, 'updateAttendance'])->name('attendances.update');
    Route::delete('/courses/{courseId}/attendances/{attendanceId}', [GuruController::class, 'destroyAttendance'])->name('attendances.destroy');

    // Bank Soal Management
    Route::get('/bank-soal', [GuruController::class, 'bankSoal'])->name('bank-soal');
    Route::get('/bank-soal/create', [GuruController::class, 'createBankSoal'])->name('bank-soal.create');
    Route::post('/bank-soal', [GuruController::class, 'storeBankSoal'])->name('bank-soal.store');
    Route::get('/bank-soal/{question}', [GuruController::class, 'showBankSoal'])->name('bank-soal.show');
    Route::get('/bank-soal/{question}/edit', [GuruController::class, 'editBankSoal'])->name('bank-soal.edit');
    Route::put('/bank-soal/{question}', [GuruController::class, 'updateBankSoal'])->name('bank-soal.update');
    Route::delete('/bank-soal/{question}', [GuruController::class, 'deleteBankSoal'])->name('bank-soal.delete');
    Route::post('/bank-soal/upload', [GuruController::class, 'uploadBankSoal'])->name('bank-soal.upload');

    // Nilai Ujian
    Route::get('/nilai-ujian', [GuruController::class, 'nilaiUjian'])->name('nilai-ujian');
    Route::get('/nilai-ujian/{resultId}/review', [GuruController::class, 'reviewExamAnswer'])->name('nilai-ujian.review');
    Route::get('/nilai-ujian/export', [GuruController::class, 'exportExamScores'])->name('nilai-ujian.export');

    // Sesi Ujian Management
    Route::get('/sesi-ujian', [GuruController::class, 'sesiUjian'])->name('sesi-ujian');
    Route::get('/sesi-ujian/create', [GuruController::class, 'createSesiUjian'])->name('sesi-ujian.create');
    Route::post('/sesi-ujian', [GuruController::class, 'storeSesiUjian'])->name('sesi-ujian.store');
    Route::get('/sesi-ujian/{sesi}', [GuruController::class, 'showSesiUjian'])->name('sesi-ujian.show');
    Route::post('/sesi-ujian/{sesi}/students', [GuruController::class, 'storeSesiUjianStudent'])->name('sesi-ujian.student.store');
    Route::post('/sesi-ujian/{sesi}/bulk-assign', [GuruController::class, 'bulkAssignSesiUjianStudents'])->name('sesi-ujian.bulk-assign');
    Route::delete('/sesi-ujian/{sesi}/students/{student}', [GuruController::class, 'destroySesiUjianStudent'])->name('sesi-ujian.student.destroy');
    Route::get('/sesi-ujian/{sesi}/edit', [GuruController::class, 'editSesiUjian'])->name('sesi-ujian.edit');
    Route::put('/sesi-ujian/{sesi}', [GuruController::class, 'updateSesiUjian'])->name('sesi-ujian.update');
    Route::delete('/sesi-ujian/{sesi}', [GuruController::class, 'destroySesiUjian'])->name('sesi-ujian.destroy');
    Route::get('/ujian/create', [GuruController::class, 'createUjian'])->name('ujian.create');
    Route::get('/ujian/{ujian}', [GuruController::class, 'showUjian'])->name('ujian.show');
    Route::post('/ujian', [GuruController::class, 'storeUjian'])->name('ujian.store');
    Route::get('/ujian/{ujian}/edit', [GuruController::class, 'editUjian'])->name('ujian.edit');
    Route::put('/ujian/{ujian}', [GuruController::class, 'updateUjian'])->name('ujian.update');
    Route::delete('/ujian/{ujian}', [GuruController::class, 'deleteUjian'])->name('ujian.delete');
    Route::get('/ujian/{ujian}/add-questions', [GuruController::class, 'addQuestionsToUjian'])->name('ujian.add-questions');
    Route::post('/ujian/{ujian}/add-questions', [GuruController::class, 'storeQuestionsToUjian'])->name('ujian.store-questions');
    Route::delete('/ujian/{ujian}/remove-question/{question}', [GuruController::class, 'removeQuestionFromUjian'])->name('ujian.remove-question');
    Route::post('/ujian/upload-image', [GuruController::class, 'uploadSoalImage'])->name('ujian.upload-image');
});

// Siswa routes
Route::middleware(['auth', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('/dashboard', [SiswaController::class, 'dashboard'])->name('dashboard');

    // Course access
    Route::get('/courses', [SiswaController::class, 'courses'])->name('courses');
    Route::get('/courses/{id}', [SiswaController::class, 'showCourse'])->name('courses.show');
    Route::get('/courses/{courseId}/lessons/{lessonId}', [SiswaController::class, 'showLesson'])->name('lessons.show');

    // Assignment access
    Route::get('/assignments', [SiswaController::class, 'assignments'])->name('assignments');
    Route::get('/assignments/{id}', [SiswaController::class, 'showAssignment'])->name('assignments.show');
    Route::post('/assignments/{id}/submit', [SiswaController::class, 'submitAssignment'])->name('assignments.submit');

    // Grades
    Route::get('/grades', [SiswaController::class, 'grades'])->name('grades');

    // Profile
    Route::get('/profile', [SiswaController::class, 'profile'])->name('profile');
    Route::patch('/profile', [SiswaController::class, 'updateProfile'])->name('profile.update');

    // Attendances
    Route::get('/attendances', [SiswaController::class, 'attendances'])->name('attendances');

    // Schedule
    Route::get('/schedule', [SiswaController::class, 'schedule'])->name('schedule');

    // Forum
    Route::get('/forum', [SiswaController::class, 'forum'])->name('forum');
    Route::post('/messages/send', [SiswaController::class, 'sendMessage'])->name('messages.send');
    Route::patch('/messages/{messageId}/read', [SiswaController::class, 'markMessageAsRead'])->name('messages.read');
    Route::get('/messages/{messageId}/edit', [SiswaController::class, 'editMessage'])->name('messages.edit');
    Route::patch('/messages/{messageId}', [SiswaController::class, 'updateMessage'])->name('messages.update');
    Route::delete('/messages/{messageId}', [SiswaController::class, 'deleteMessage'])->name('messages.destroy');

    Route::get('/ujian', [SiswaController::class, 'ujian'])->name('ujian');
    Route::get('/ujian/{sesi}/mulai', [SiswaController::class, 'startUjian'])->name('ujian.mulai');
    Route::post('/ujian/{sesi}/submit', [SiswaController::class, 'submitUjian'])->name('ujian.submit');
    Route::get('/ujian/result/{result}/pdf', [SiswaController::class, 'downloadResultPdf'])->name('ujian.result-pdf');

    // Announcements
    Route::get('/announcements', [SiswaController::class, 'announcements'])->name('announcements');

    // File downloads
    Route::get('/download/{type}/{filename}', [SiswaController::class, 'downloadFile'])->name('download');
});
