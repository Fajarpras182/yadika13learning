<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Course;
use App\Models\Question;
use App\Models\Ujian;
use App\Models\UjianResult;
use App\Models\SesiUjian;
use App\Models\Grade;
use App\Models\Assignment;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Message;
use App\Models\Schedule;
use App\Models\SchoolClass;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Illuminate\Support\Str;

class GuruController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:guru');
    }

    public function dashboard()
    {
        $user = auth()->user();

        // Get courses taught by this guru
        $courses = Course::where('guru_id', $user->id)->get();
        $recentCourses = $courses;

        // Get total courses
        $totalCourses = $courses->count();

        // Get total lessons across all courses
        $totalLessons = Lesson::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->count();

        // Get total students across all courses
        $totalStudents = SchoolClass::whereHas('courses', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->withCount('students')->get()->sum('students_count');

        // Get total assignments
        $totalAssignments = Assignment::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->count();

        // Get pending assignments to grade
        $pendingGrades = Grade::whereHas('assignment.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->whereNull('nilai')->count();

        // Get recent exam results
        $recentExamResults = UjianResult::whereHas('sesiUjian.ujian.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['student', 'sesiUjian.ujian'])->latest()->take(5)->get();

        // Get today's schedule
        $todayDay = strtolower(now()->format('l')); // Get day name in lowercase
        $todaySchedule = \App\Models\Schedule::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->where('day', $todayDay)
            ->with(['course', 'schoolClass.major'])
            ->orderBy('start_time')
            ->get();

        // Get unread messages count
        $unreadMessages = Message::where('is_read', false)->count();

        return view('guru.dashboard', compact(
            'courses', 'recentCourses', 'totalCourses', 'totalLessons', 'totalStudents', 'totalAssignments',
            'pendingGrades', 'recentExamResults', 'todaySchedule', 'unreadMessages'
        ));
    }

    /* ====================== PROFILE ======================== */
    public function profile()
    {
        return view('guru.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'no_hp'             => 'nullable|string|max:20',
            'alamat'            => 'nullable|string|max:500',
            'jenis_kelamin'     => 'required|in:L,P',
            'agama'             => 'nullable|string|max:50',
        ]);

        $user->update($request->only([
            'name', 'email', 'no_hp', 'alamat', 'jenis_kelamin', 'agama'
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully');
    }

    /* ====================== MESSAGES/FORUM ======================== */
    public function forum()
    {
        $messages = Message::with('user')->latest()->paginate(20);
        return view('guru.messages.index', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        Message::create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Message sent successfully');
    }

    public function markMessageAsRead($messageId)
    {
        $message = Message::findOrFail($messageId);
        // Assuming there's a read status, but for now just return success
        return response()->json(['success' => true]);
    }

    public function editMessage($messageId)
    {
        $message = Message::where('user_id', auth()->id())->findOrFail($messageId);
        return view('guru.messages.edit', compact('message'));
    }

    public function updateMessage(Request $request, $messageId)
    {
        $message = Message::where('user_id', auth()->id())->findOrFail($messageId);

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message->update(['content' => $request->content]);

        return redirect()->route('guru.messages')->with('success', 'Message updated successfully');
    }

    public function deleteMessage($messageId)
    {
        $message = Message::where('user_id', auth()->id())->findOrFail($messageId);
        $message->delete();

        return redirect()->back()->with('success', 'Message deleted successfully');
    }

    /* ====================== REPORTS ======================== */
    public function reports()
    {
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->with('schoolClass')->get();

        return view('guru.reports.index', compact('courses'));
    }

    public function attendanceReports()
    {
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->with('schoolClass')->get();

        return view('guru.reports.attendance', compact('courses'));
    }

    public function exportAttendancePdf()
    {
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->with(['schoolClass.students', 'attendances'])->get();

        $pdf = Pdf::loadView('guru.reports.attendance-pdf', compact('courses'));
        return $pdf->download('attendance-report.pdf');
    }

    public function exportGradesPdf($courseId = null)
    {
        $user = auth()->user();
        $query = Course::where('guru_id', $user->id);

        if ($courseId) {
            $query->where('id', $courseId);
        }

        $courses = $query->with(['assignments.grades.student'])->get();

        $pdf = Pdf::loadView('guru.reports.grades-pdf', compact('courses'));
        return $pdf->download('grades-report.pdf');
    }

    public function exportGradesExcel($courseId = null)
    {
        $user = auth()->user();
        $query = Course::where('guru_id', $user->id);

        if ($courseId) {
            $query->where('id', $courseId);
        }

        $courses = $query->with(['assignments.grades.student'])->get();

        return Excel::download(new \App\Exports\GradesExport($courses), 'grades-report.xlsx');
    }

    public function exportGradesWord($courseId = null)
    {
        // Similar to PDF but for Word format
        return $this->exportGradesPdf($courseId);
    }

    public function rekaptugaspdf()
    {
        $user = auth()->user();
        // Get all grades for courses taught by this guru
        $grades = Grade::whereHas('assignment.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['assignment', 'assignment.course', 'student'])->get();

        return view('guru.rekap-nilai-tugas', compact('grades'));
    }

    /* ====================== COURSES/SUBJECTS ======================== */
    public function courses()
    {
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->with('schoolClass')->paginate(10);

        return view('guru.courses.index', compact('courses'));
    }

    public function createCourse()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $majors = \App\Models\Major::where('is_active', true)->orderBy('name')->get();
        return view('guru.courses.create', compact('classes', 'majors'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'kode_mata_pelajaran' => 'required|string|max:10|unique:courses',
            'nama_mata_pelajaran' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'major_id' => 'required|exists:majors,id',
            'semester' => 'required|in:1,2,3,4,5,6',
            'sks' => 'required|integer|min:1|max:6',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Course::create([
            'kode_mata_pelajaran' => $request->kode_mata_pelajaran,
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id' => auth()->id(),
            'class_id' => $request->class_id,
            'major_id' => $request->major_id,
            'semester' => $request->semester,
            'sks' => $request->sks,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('guru.courses')->with('success', 'Course created successfully');
    }

    public function editCourse(Course $course)
    {
        // Check if course belongs to this guru
        if ($course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $majors = \App\Models\Major::where('is_active', true)->orderBy('name')->get();
        return view('guru.courses.edit', compact('course', 'classes', 'majors'));
    }

    public function updateCourse(Request $request, Course $course)
    {
        // Check if course belongs to this guru
        if ($course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'kode_mata_pelajaran' => 'required|string|max:10|unique:courses,kode_mata_pelajaran,' . $course->id,
            'nama_mata_pelajaran' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'major_id' => 'required|exists:majors,id',
            'semester' => 'required|in:1,2,3,4,5,6',
            'sks' => 'required|integer|min:1|max:6',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $course->update($request->only([
            'kode_mata_pelajaran', 'nama_mata_pelajaran', 'class_id', 'major_id',
            'semester', 'sks', 'deskripsi', 'is_active'
        ]));

        return redirect()->route('guru.courses')->with('success', 'Course updated successfully');
    }

    public function destroyCourse(Course $course)
    {
        // Check if course belongs to this guru
        if ($course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $course->delete();

        return redirect()->route('guru.courses')->with('success', 'Course deleted successfully');
    }

    /* ====================== LESSONS ======================== */
    public function lessons($courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $lessons = Lesson::where('course_id', $courseId)->orderBy('urutan')->paginate(15);

        return view('guru.lessons.index', compact('course', 'lessons'));
    }

    public function createLesson($courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        return view('guru.lessons.create', compact('course'));
    }

    public function storeLesson(Request $request, $courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'materi' => 'required|string',
            'file_materi' => 'nullable|file|max:51200',
            'video_url' => 'nullable|url',
            'urutan' => 'required|integer|min:1',
        ]);

        $data = [
            'course_id' => $courseId,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'materi' => $request->materi,
            'urutan' => $request->urutan,
            'is_published' => true,
        ];

        // Handle video URL
        if ($request->video_url) {
            $data['video_url'] = $request->video_url;
        }

        // Handle file upload
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lessons', $fileName, 'public');
            $data['file_materi'] = $filePath;
        }

        Lesson::create($data);

        return redirect()->route('guru.lessons', $courseId)->with('success', 'Materi berhasil ditambahkan');
    }

    public function editLesson($courseId, $lessonId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $lesson = Lesson::where('course_id', $courseId)->findOrFail($lessonId);

        return view('guru.lessons.edit', compact('course', 'lesson'));
    }

    public function updateLesson(Request $request, $courseId, $lessonId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $lesson = Lesson::where('course_id', $courseId)->findOrFail($lessonId);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'materi' => 'required|string',
            'file_materi' => 'nullable|file|max:51200',
            'video_url' => 'nullable|url',
            'urutan' => 'required|integer|min:1',
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'materi' => $request->materi,
            'urutan' => $request->urutan,
        ];

        // Handle video URL
        if ($request->video_url) {
            $data['video_url'] = $request->video_url;
        }

        // Handle file upload
        if ($request->hasFile('file_materi')) {
            $file = $request->file('file_materi');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lessons', $fileName, 'public');
            $data['file_materi'] = $filePath;
        }

        $lesson->update($data);

        return redirect()->route('guru.lessons', $courseId)->with('success', 'Materi berhasil diperbarui');
    }

    public function destroyLesson($courseId, $lessonId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $lesson = Lesson::where('course_id', $courseId)->findOrFail($lessonId);

        $lesson->delete();

        return redirect()->route('guru.lessons', $courseId)->with('success', 'Lesson deleted successfully');
    }

    /* ====================== ASSIGNMENTS ======================== */
    public function assignments($courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $assignments = Assignment::where('course_id', $courseId)->with('grades')->paginate(15);

        return view('guru.assignments.index', compact('course', 'assignments'));
    }

    public function createAssignment($courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        return view('guru.assignments.create', compact('course'));
    }

    public function storeAssignment(Request $request, $courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date|after:today',
            'bobot_nilai' => 'nullable|numeric|min:0|max:100',
            'file' => 'nullable|file|max:51200',
        ]);

        $data = [
            'course_id' => $courseId,
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
            'bobot_nilai' => $request->bobot_nilai ?? 100,
            'instruksi' => null,
        ];

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
            $data['file_tugas'] = $filePath;
        }

        Assignment::create($data);

        return redirect()->route('guru.assignments', $courseId)->with('success', 'Assignment created successfully');
    }

    public function assignmentStatus($assignmentId)
    {
        $assignment = Assignment::whereHas('course', function($query) {
            $query->where('guru_id', auth()->id());
        })->findOrFail($assignmentId);

        $grades = Grade::where('assignment_id', $assignmentId)->with('student')->get();

        return view('guru.assignments.status', compact('assignment', 'grades'));
    }

    public function gradeAssignment($assignmentId)
    {
        $assignment = Assignment::whereHas('course', function($query) {
            $query->where('guru_id', auth()->id());
        })->findOrFail($assignmentId);

        $grades = Grade::where('assignment_id', $assignmentId)->with('student')->get();

        return view('guru.assignments.grade', compact('assignment', 'grades'));
    }

    public function updateGrade(Request $request, $gradeId)
    {
        $grade = Grade::whereHas('assignment.course', function($query) {
            $query->where('guru_id', auth()->id());
        })->with('assignment')->findOrFail($gradeId);

        $request->validate([
            'nilai' => 'required|numeric|min:0|max:' . $grade->assignment->bobot_nilai,
            'feedback' => 'nullable|string|max:1000',
        ]);

        $grade->update([
            'nilai' => $request->nilai,
            'feedback' => $request->feedback,
        ]);

        return redirect()->back()->with('success', 'Grade updated successfully');
    }

    public function bulkUpdateGrades(Request $request, $assignmentId)
    {
        $assignment = Assignment::whereHas('course', function($query) {
            $query->where('guru_id', auth()->id());
        })->findOrFail($assignmentId);

        // Validate input data
        $request->validate([
            'nilai' => 'nullable|array',
            'nilai.*' => 'nullable|numeric|min:0|max:' . $assignment->bobot_nilai,
            'feedback' => 'nullable|array',
            'feedback.*' => 'nullable|string|max:1000',
        ]);

        // Get all grade IDs for this assignment
        $nilaiData = $request->input('nilai', []);
        $feedbackData = $request->input('feedback', []);

        // Update each grade
        foreach ($nilaiData as $gradeId => $nilai) {
            $grade = Grade::findOrFail($gradeId);
            
            // Verify that this grade belongs to the assignment and course
            if ($grade->assignment_id == $assignmentId) {
                $updateData = [];
                
                if ($nilai !== null && $nilai !== '') {
                    $updateData['nilai'] = $nilai;
                    $updateData['status'] = 'sudah_dinilai'; // Mark as graded
                }
                
                if (isset($feedbackData[$gradeId]) && $feedbackData[$gradeId] !== null) {
                    $updateData['feedback'] = $feedbackData[$gradeId];
                }
                
                if (!empty($updateData)) {
                    $grade->update($updateData);
                }
            }
        }

        return redirect()->back()->with('success', 'Semua nilai berhasil disimpan!');
    }

    public function destroyGrade($gradeId)
    {
        $grade = Grade::whereHas('assignment.course', function($query) {
            $query->where('guru_id', auth()->id());
        })->findOrFail($gradeId);

        $grade->delete();

        return redirect()->back()->with('success', 'Grade deleted successfully');
    }

    public function editAssignment($courseId, $assignmentId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $assignment = Assignment::where('course_id', $courseId)->findOrFail($assignmentId);

        return view('guru.assignments.edit', compact('course', 'assignment'));
    }

    public function updateAssignment(Request $request, $courseId, $assignmentId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $assignment = Assignment::where('course_id', $courseId)->findOrFail($assignmentId);

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'deadline' => 'required|date',
            'bobot_nilai' => 'nullable|numeric|min:0|max:100',
        ]);

        $assignment->update($request->only(['judul', 'deskripsi', 'deadline', 'bobot_nilai']));

        return redirect()->route('guru.assignments', $courseId)->with('success', 'Assignment updated successfully');
    }

    public function destroyAssignment($courseId, $assignmentId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $assignment = Assignment::where('course_id', $courseId)->findOrFail($assignmentId);

        $assignment->delete();

        return redirect()->route('guru.assignments', $courseId)->with('success', 'Assignment deleted successfully');
    }

    /* ====================== ATTENDANCE ======================== */
    public function attendances($courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $attendances = Attendance::where('course_id', $courseId)->with('student')->get();

        return view('guru.attendances.index', compact('course', 'attendances'));
    }

    public function createAttendance($courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $students = $course->schoolClass->students ?? collect();

        return view('guru.attendances.create', compact('course', 'students'));
    }

    public function storeAttendance(Request $request, $courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);

        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:present,absent,late',
        ]);

        foreach ($request->attendances as $attendanceData) {
            Attendance::create([
                'course_id' => $courseId,
                'student_id' => $attendanceData['student_id'],
                'date' => $request->date,
                'status' => $attendanceData['status'],
            ]);
        }

        return redirect()->route('guru.attendances', $courseId)->with('success', 'Attendance recorded successfully');
    }

    public function editAttendance($courseId, $attendanceId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $attendance = Attendance::where('course_id', $courseId)->findOrFail($attendanceId);

        return view('guru.attendances.edit', compact('course', 'attendance'));
    }

    public function updateAttendance(Request $request, $courseId, $attendanceId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $attendance = Attendance::where('course_id', $courseId)->findOrFail($attendanceId);

        $request->validate([
            'status' => 'required|in:present,absent,late',
        ]);

        $attendance->update(['status' => $request->status]);

        return redirect()->route('guru.attendances', $courseId)->with('success', 'Attendance updated successfully');
    }

    public function destroyAttendance($courseId, $attendanceId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $attendance = Attendance::where('course_id', $courseId)->findOrFail($attendanceId);

        $attendance->delete();

        return redirect()->route('guru.attendances', $courseId)->with('success', 'Attendance deleted successfully');
    }

    /* ====================== BANK SOAL ======================== */
    public function bankSoal()
    {
        $questions = Question::where('created_by', auth()->id())->paginate(20);
        return view('guru.bank-soal.index', compact('questions'));
    }

    public function createBankSoal()
    {
        $courses = Course::where('guru_id', auth()->id())->get();
        return view('guru.bank-soal.create', compact('courses'));
    }

    public function storeBankSoal(Request $request)
    {
        $request->validate([
            'pertanyaan' => 'required|string',
            'jawaban_a' => 'required|string',
            'jawaban_b' => 'required|string',
            'jawaban_c' => 'required|string',
            'jawaban_d' => 'required|string',
            'jawaban_e' => 'required|string',
            'kunci_jawaban' => 'required|in:a,b,c,d,e',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        Question::create([
            'pertanyaan' => $request->pertanyaan,
            'jawaban_a' => $request->jawaban_a,
            'jawaban_b' => $request->jawaban_b,
            'jawaban_c' => $request->jawaban_c,
            'jawaban_d' => $request->jawaban_d,
            'jawaban_e' => $request->jawaban_e,
            'kunci_jawaban' => $request->kunci_jawaban,
            'course_id' => $request->course_id,
            'guru_id' => auth()->id(),
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('guru.bank-soal')->with('success', 'Soal berhasil ditambahkan ke bank soal');
    }

    public function showBankSoal(Question $question)
    {
        // Check if question belongs to this guru
        if ($question->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        return view('guru.bank-soal.show', compact('question'));
    }

    public function editBankSoal(Question $question)
    {
        // Check if question belongs to this guru
        if ($question->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $courses = Course::where('guru_id', auth()->id())->get();
        return view('guru.bank-soal.edit', compact('question', 'courses'));
    }

    public function updateBankSoal(Request $request, Question $question)
    {
        // Check if question belongs to this guru
        if ($question->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'pertanyaan' => 'required|string',
            'jawaban_a' => 'required|string',
            'jawaban_b' => 'required|string',
            'jawaban_c' => 'required|string',
            'jawaban_d' => 'required|string',
            'jawaban_e' => 'required|string',
            'kunci_jawaban' => 'required|in:a,b,c,d,e',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $question->update([
            'pertanyaan' => $request->pertanyaan,
            'jawaban_a' => $request->jawaban_a,
            'jawaban_b' => $request->jawaban_b,
            'jawaban_c' => $request->jawaban_c,
            'jawaban_d' => $request->jawaban_d,
            'jawaban_e' => $request->jawaban_e,
            'kunci_jawaban' => $request->kunci_jawaban,
            'course_id' => $request->course_id,
        ]);

        return redirect()->route('guru.bank-soal')->with('success', 'Soal berhasil diperbarui');
    }

    public function deleteBankSoal(Question $question)
    {
        // Check if question belongs to this guru
        if ($question->created_by !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $question->delete();

        return redirect()->route('guru.bank-soal')->with('success', 'Soal berhasil dihapus');
    }

    public function uploadBankSoal(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx',
        ]);

        // Use the BankSoalDocumentImport service
        $import = new \App\Services\BankSoalDocumentImport();
        Excel::import($import, $request->file('file'));

        return redirect()->route('guru.bank-soal')->with('success', 'Soal berhasil diimpor');
    }

    /* ====================== NILAI UJIAN ======================== */
    public function nilaiUjian()
    {
        $user = auth()->user();

        // Get all exam results for courses taught by this guru
        $examResults = UjianResult::whereHas('sesiUjian.ujian.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['student', 'sesiUjian.ujian.course', 'ujian'])->latest()->get();

        return view('guru.nilai-ujian.index', compact('examResults'));
    }

    public function reviewExamAnswer($resultId)
    {
        $user = auth()->user();
        
        $result = UjianResult::with(['student', 'sesiUjian.ujian', 'answers.question'])
            ->whereHas('sesiUjian.ujian.course', function($query) use ($user) {
                $query->where('guru_id', $user->id);
            })
            ->findOrFail($resultId);

        // Get all questions for this exam
        $questions = Question::where('ujian_id', $result->ujian->id)
            ->with(['studentAnswers' => function($q) use ($result) {
                $q->where('ujian_result_id', $result->id);
            }])
            ->orderBy('nomor_soal')
            ->get();

        $correctCount = $result->answers()->where('is_correct', true)->count();
        $totalCount = $result->answers()->count();

        return view('guru.nilai-ujian.review', compact('result', 'questions', 'correctCount', 'totalCount'));
    }

    public function exportExamScores(Request $request)
    {
        $user = auth()->user();
        
        $query = UjianResult::whereHas('sesiUjian.ujian.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['student', 'sesiUjian.ujian', 'ujian', 'sesiUjian']);

        // Filter by class if provided
        if ($request->has('class_id') && $request->class_id) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('class_id', $request->class_id);
            });
        }

        $results = $query->get();

        $pdf = Pdf::loadView('guru.exports.exam_grades_pdf', compact('results'));
        return $pdf->download('nilai-ujian-' . date('Ymd-His') . '.pdf');
    }

    /* ====================== STUDENT ASSIGNMENTS ======================== */
    public function allAssignments()
    {
        $user = auth()->user();

        $assignments = Assignment::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['course', 'grades.student'])->get();

        return view('guru.student-assignments.index', compact('assignments'));
    }

    /* ====================== SESI UJIAN ======================== */
    public function sesiUjian()
    {
        $user = auth()->user();
        $sesiUjians = SesiUjian::whereHas('ujian.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['ujian.course'])->paginate(10);

        return view('guru.sesi-ujian.index', compact('sesiUjians'));
    }

    public function createSesiUjian()
    {
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->get();
        $ujians = Ujian::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->get();

        return view('guru.sesi-ujian.create', compact('courses', 'ujians'));
    }

    public function storeSesiUjian(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'ujian_id' => 'required|exists:ujians,id',
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        // Verify the ujian belongs to this guru
        $ujian = Ujian::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->findOrFail($request->ujian_id);

        SesiUjian::create($request->only([
            'ujian_id', 'name', 'start_time', 'end_time', 'duration_minutes', 'instructions'
        ]));

        return redirect()->route('guru.sesi-ujian')->with('success', 'Exam session created successfully');
    }

    public function showSesiUjian(SesiUjian $sesi)
    {
        // Check if sesi ujian belongs to this guru's course
        if ($sesi->ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $sesi->load(['ujian.course', 'results.student']);

        return view('guru.sesi-ujian.show', compact('sesi'));
    }

    public function storeSesiUjianStudent(Request $request, SesiUjian $sesi)
    {
        // Check if sesi ujian belongs to this guru's course
        if ($sesi->ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        // Check if student is already assigned
        if ($sesi->results()->where('student_id', $request->student_id)->exists()) {
            return redirect()->back()->with('error', 'Student is already assigned to this exam session');
        }

        $sesi->results()->create([
            'student_id' => $request->student_id,
            'status' => 'assigned',
        ]);

        return redirect()->back()->with('success', 'Student assigned successfully');
    }

    public function bulkAssignSesiUjianStudents(Request $request, SesiUjian $sesi)
    {
        // Check if sesi ujian belongs to this guru's course
        if ($sesi->ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->student_ids as $studentId) {
            if (!$sesi->results()->where('student_id', $studentId)->exists()) {
                $sesi->results()->create([
                    'student_id' => $studentId,
                    'status' => 'assigned',
                ]);
            }
        }

        return redirect()->back()->with('success', 'Students assigned successfully');
    }

    public function destroySesiUjianStudent(SesiUjian $sesi, $studentId)
    {
        // Check if sesi ujian belongs to this guru's course
        if ($sesi->ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $result = $sesi->results()->where('student_id', $studentId)->first();
        if ($result) {
            $result->delete();
        }

        return redirect()->back()->with('success', 'Student removed from exam session');
    }

    public function editSesiUjian(SesiUjian $sesi)
    {
        // Check if sesi ujian belongs to this guru's course
        if ($sesi->ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->get();
        $ujians = Ujian::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->get();

        return view('guru.sesi-ujian.edit', compact('sesi', 'courses', 'ujians'));
    }

    public function updateSesiUjian(Request $request, SesiUjian $sesi)
    {
        // Check if sesi ujian belongs to this guru's course
        if ($sesi->ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'ujian_id' => 'required|exists:ujians,id',
            'name' => 'required|string|max:255',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'duration_minutes' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        $sesi->update($request->only([
            'ujian_id', 'name', 'start_time', 'end_time', 'duration_minutes', 'instructions'
        ]));

        return redirect()->route('guru.sesi-ujian')->with('success', 'Exam session updated successfully');
    }

    public function destroySesiUjian(SesiUjian $sesi)
    {
        // Check if sesi ujian belongs to this guru's course
        if ($sesi->ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $sesi->delete();

        return redirect()->route('guru.sesi-ujian')->with('success', 'Exam session deleted successfully');
    }

    /* ====================== UJIAN ======================== */
    public function createUjian()
    {
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->get();

        return view('guru.ujian.create', compact('courses'));
    }

    public function showUjian(Ujian $ujian)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $ujian->load(['course', 'questions', 'sesiUjians']);

        return view('guru.ujian.show', compact('ujian'));
    }

    public function storeUjian(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_questions' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        // Verify the course belongs to this guru
        $course = Course::where('guru_id', $user->id)->findOrFail($request->course_id);

        Ujian::create($request->only([
            'course_id', 'name', 'description', 'total_questions', 'duration_minutes'
        ]));

        return redirect()->route('guru.sesi-ujian')->with('success', 'Exam created successfully');
    }

    public function editUjian(Ujian $ujian)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->get();

        return view('guru.ujian.edit', compact('ujian', 'courses'));
    }

    public function updateUjian(Request $request, Ujian $ujian)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_questions' => 'required|integer|min:1',
            'duration_minutes' => 'required|integer|min:1',
        ]);

        $ujian->update($request->only([
            'course_id', 'name', 'description', 'total_questions', 'duration_minutes'
        ]));

        return redirect()->route('guru.sesi-ujian')->with('success', 'Exam updated successfully');
    }

    public function deleteUjian(Ujian $ujian)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $ujian->delete();

        return redirect()->route('guru.sesi-ujian')->with('success', 'Exam deleted successfully');
    }

    public function addQuestionsToUjian(Ujian $ujian)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $questions = Question::where('created_by', auth()->id())->get();

        return view('guru.ujian.add-questions', compact('ujian', 'questions'));
    }

    public function storeQuestionsToUjian(Request $request, Ujian $ujian)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $ujian->questions()->syncWithoutDetaching($request->question_ids);

        return redirect()->route('guru.ujian.show', $ujian)->with('success', 'Questions added to exam successfully');
    }

    public function removeQuestionFromUjian(Ujian $ujian, Question $question)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }
        $ujian->questions()->detach($question->id);

        return redirect()->route('guru.ujian.show', $ujian)->with('success', 'Question removed from exam successfully');
    }

    public function uploadSoalImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('soal_images', $filename, 'public');

            return response()->json([
                'success' => true,
                'file' => [
                    'name' => $filename,
                    'url' => asset('storage/' . $path)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()
            ], 400);
        }
    }
}
