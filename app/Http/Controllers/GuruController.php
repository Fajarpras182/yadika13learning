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
        $totalStudents = $courses->sum('student_count');

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

        // Get ongoing exam sessions for real-time monitoring
        $ongoingExamSessions = SesiUjian::whereHas('ujian.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->where('waktu_mulai', '<=', now())
            ->where('waktu_selesai', '>=', now())
            ->with(['ujian.course', 'students'])
            ->get();

        // Get exam analytics for dashboard
        $examAnalytics = $this->getExamAnalytics($user->id);

        return view('guru.dashboard', compact(
            'courses', 'recentCourses', 'totalCourses', 'totalLessons', 'totalStudents', 'totalAssignments',
            'pendingGrades', 'recentExamResults', 'todaySchedule', 'unreadMessages', 'ongoingExamSessions',
            'examAnalytics'
        ));
    }

    /**
     * Get exam analytics for dashboard
     */
    private function getExamAnalytics($guruId)
    {
        // Get exam statistics for the last 30 days
        $thirtyDaysAgo = now()->subDays(30);
        
        $exams = Ujian::whereHas('course', function($query) use ($guruId) {
            $query->where('guru_id', $guruId);
        })->with(['results'])->get();

        $totalExams = $exams->count();
        $totalExamsTaken = $exams->sum(function($exam) {
            return $exam->results->count();
        });

        $averageScore = 0;
        if ($totalExamsTaken > 0) {
            $totalScores = $exams->sum(function($exam) {
                return $exam->results->sum('score');
            });
            $averageScore = round($totalScores / $totalExamsTaken, 2);
        }

        return [
            'total_exams' => $totalExams,
            'total_exams_taken' => $totalExamsTaken,
            'average_score' => $averageScore,
        ];
    }

    /**
     * Show detailed analytics dashboard
     */
    public function analyticsDetail()
    {
        $user = auth()->user();

        // Get exam statistics
        $exams = Ujian::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['results', 'course'])->get();

        // Calculate overall statistics
        $totalExams = $exams->count();
        $totalExamsTaken = $exams->sum(function($exam) { return $exam->results->count(); });
        
        $scoreStats = [
            'average' => 0,
            'highest' => 0,
            'lowest' => 0,
            'above_70' => 0,
            'above_80' => 0,
            'above_90' => 0,
        ];

        if ($totalExamsTaken > 0) {
            $allScores = $exams->flatMap(function($exam) {
                return $exam->results->pluck('score');
            });
            
            $scoreStats['average'] = round($allScores->avg(), 2);
            $scoreStats['highest'] = $allScores->max();
            $scoreStats['lowest'] = $allScores->min();
            $scoreStats['above_70'] = $allScores->filter(fn($s) => $s >= 70)->count();
            $scoreStats['above_80'] = $allScores->filter(fn($s) => $s >= 80)->count();
            $scoreStats['above_90'] = $allScores->filter(fn($s) => $s >= 90)->count();
        }

        // Get course-wise statistics
        $courseStats = $exams->map(function($exam) {
            $results = $exam->results;
            return [
                'course_id' => $exam->course_id,
                'course_name' => $exam->course->nama_mata_pelajaran,
                'exam_title' => $exam->judul,
                'total_taken' => $results->count(),
                'average_score' => $results->count() > 0 ? round($results->avg('score'), 2) : 0,
                'pass_rate' => $results->count() > 0 ? round(($results->where('score', '>=', 70)->count() / $results->count()) * 100, 2) : 0,
            ];
        })->values();

        // Get recent exam results
        $recentResults = UjianResult::whereHas('sesiUjian.ujian.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['student', 'sesiUjian.ujian.course'])->latest()->take(10)->get();

        // Get assignment statistics
        $assignments = Assignment::whereHas('course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->with(['grades'])->get();

        $assignmentStats = [
            'total' => $assignments->count(),
            'graded' => $assignments->sum(fn($a) => $a->grades->where('nilai', '!=', null)->count()),
            'pending' => $assignments->sum(fn($a) => $a->grades->where('nilai', null)->count()),
        ];

        return view('guru.analytics', compact(
            'totalExams', 'totalExamsTaken', 'scoreStats', 'courseStats', 
            'recentResults', 'assignmentStats'
        ));
    }

    /* ====================== PROFILE ======================== */
    public function profile()
    {
        $guru = auth()->user();
        return view('guru.profile', compact('guru'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nis_nip'           => 'nullable|string|max:20',
            'no_hp'             => 'nullable|string|max:20',
            'alamat'            => 'nullable|string|max:500',
            'jenis_kelamin'     => 'nullable|in:L,P',
            'agama'             => 'nullable|string|max:50',
            'current_password'  => 'nullable|string',
            'password'          => 'nullable|string|min:8|confirmed',
        ]);

        // Check current password if new password is provided
        if ($request->filled('password') && !\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'name'              => $request->name,
            'email'             => $request->email,
            'nis_nip'           => $request->nis_nip,
            'no_hp'             => $request->no_hp,
            'alamat'            => $request->alamat,
            'jenis_kelamin'     => $request->jenis_kelamin,
            'agama'             => $request->agama,
            'password'          => $request->filled('password') ? bcrypt($request->password) : $user->password,
        ]);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    /* ====================== MESSAGES/FORUM ======================== */
    public function forum()
    {
        $messages = Message::whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(20);

        if (request()->ajax()) {
            return view('guru.messages._list', compact('messages'));
        }

        return view('guru.messages.index', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:messages,id',
        ]);

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => auth()->id(), // Use self as placeholder for forum
            'subject' => 'Forum',
            'message' => $request->message,
            'content' => $request->message,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil dikirim');
    }

    public function markMessageAsRead($messageId)
    {
        $message = Message::findOrFail($messageId);
        $message->markAsRead();
        return response()->json(['success' => true]);
    }

    public function deleteMessage($messageId)
    {
        $message = Message::where('sender_id', auth()->id())->findOrFail($messageId);
        $message->delete();
        return redirect()->back()->with('success', 'Pesan berhasil dihapus');
    }

    public function editMessage($messageId)
    {
        $message = Message::where('sender_id', auth()->id())->findOrFail($messageId);
        return view('guru.messages.edit', compact('message'));
    }

    public function updateMessage(Request $request, $messageId)
    {
        $message = Message::where('sender_id', auth()->id())->findOrFail($messageId);

        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message->update([
            'message' => $request->message,
            'content' => $request->message
        ]);

        return redirect()->route('guru.messages')->with('success', 'Pesan berhasil diperbarui');
    }

    /* ====================== REPORTS ======================== */
    public function reports()
    {
        $user = auth()->user();
        $courses = Course::where('guru_id', $user->id)->with('schoolClass')->get();

        return view('guru.reports.index', compact('courses'));
    }

    public function attendanceReports(Request $request)
    {
        $user = auth()->user();
        $classId = $request->get('class_id');
        $search = $request->get('search');

        // Get classes taught by this guru
        $courseIds = Course::where('guru_id', $user->id)->pluck('id');
        $classIdsFromCourses = Course::where('guru_id', $user->id)->whereNotNull('class_id')->pluck('class_id');
        $classIdsFromSchedules = Schedule::whereIn('course_id', $courseIds)->pluck('class_id');
        $allClassIds = $classIdsFromCourses->concat($classIdsFromSchedules)->unique()->filter();
        $classes = SchoolClass::whereIn('id', $allClassIds)->get();

        $query = Attendance::whereHas('course', function($q) use ($user) {
            $q->where('guru_id', $user->id);
        })->with(['course', 'student']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('class_id')) {
            $query->whereHas('student', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $attendances = $query->orderBy('tanggal', 'desc')->get();

        return view('guru.reports.attendance', compact('classes', 'attendances', 'classId', 'search'));
    }

    public function exportAttendancePdf(Request $request)
    {
        $user = auth()->user();
        $courseQuery = Course::where('guru_id', $user->id)->with('schoolClass');
        $course = $request->filled('course_id')
            ? $courseQuery->find($request->course_id)
            : $courseQuery->first();

        if (! $course) {
            abort(404, 'Course not found.');
        }

        $attendanceQuery = Attendance::where('course_id', $course->id)->with('student');

        if ($request->filled('tanggal_from')) {
            $attendanceQuery->whereDate('tanggal', '>=', $request->tanggal_from);
        }

        if ($request->filled('tanggal_to')) {
            $attendanceQuery->whereDate('tanggal', '<=', $request->tanggal_to);
        }

        if ($request->filled('status')) {
            $attendanceQuery->where('status', $request->status);
        }

        $attendances = $attendanceQuery->orderBy('tanggal', 'desc')->get();
        $students = $course->students ?? collect();
        $attendanceSummary = $attendances->groupBy('student_id')->map(function ($items) {
            return $items->groupBy('status')->map(function ($group, $status) {
                return collect(['status' => $status, 'count' => $group->count()]);
            })->values();
        });

        $pdf = Pdf::loadView('guru.reports.attendance-pdf', compact('course', 'students', 'attendanceSummary'));
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

        $gradeData = collect();
        foreach ($courses as $course) {
            foreach ($course->assignments as $assignment) {
                foreach ($assignment->grades as $grade) {
                    $gradeData->push([
                        'mata_pelajaran' => $course->nama_mata_pelajaran,
                        'tugas' => $assignment->judul ?? $assignment->nama ?? 'Tugas',
                        'nama_siswa' => $grade->student->name ?? '-',
                        'nilai' => $grade->nilai,
                    ]);
                }
            }
        }

        $pdf = Pdf::loadView('guru.reports.grades-pdf', compact('gradeData'));
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

    public function rekaptugaspdf(Request $request)
    {
        $user = auth()->user();
        $assignmentType = $request->get('assignment_type', 'tugas');
        $classId = $request->get('class_id');
        $search = $request->get('search');

        // Get classes taught by this guru for the filter dropdown
        // Logic: Classes can be linked directly to Course or via Schedules
        $courseIds = Course::where('guru_id', $user->id)->pluck('id');
        $classIdsFromCourses = Course::where('guru_id', $user->id)->whereNotNull('class_id')->pluck('class_id');
        $classIdsFromSchedules = Schedule::whereIn('course_id', $courseIds)->pluck('class_id');
        $allClassIds = $classIdsFromCourses->concat($classIdsFromSchedules)->unique()->filter();
        
        $classes = SchoolClass::whereIn('id', $allClassIds)->get();

        $query = Course::where('guru_id', $user->id);
        
        if ($request->filled('class_id')) {
            $query->where(function($q) use ($classId) {
                $q->where('class_id', $classId)
                  ->orWhereHas('schedules', function($sq) use ($classId) {
                      $sq->where('class_id', $classId);
                  });
            });
        }

        $courses = $query->with(['assignments' => function($q) use ($request, $search) {
            if ($request->filled('tanggal_from')) {
                $q->whereDate('created_at', '>=', $request->tanggal_from);
            }
            if ($request->filled('tanggal_to')) {
                $q->whereDate('created_at', '<=', $request->tanggal_to);
            }
            
            $q->with(['grades' => function($gq) use ($search) {
                if ($search) {
                    $gq->whereHas('student', function($sq) use ($search) {
                        $sq->where('name', 'like', '%' . $search . '%');
                    });
                }
                $gq->with('student');
            }]);
        }])->get();

        return view('guru.rekap-nilai-tugas', compact('courses', 'assignmentType', 'classes', 'classId', 'search'));
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
    public function attendances(Request $request, $courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        
        $query = Attendance::where('course_id', $courseId)->with('student');

        if ($request->filled('tanggal_from')) {
            $query->whereDate('tanggal', '>=', $request->tanggal_from);
        }
        if ($request->filled('tanggal_to')) {
            $query->whereDate('tanggal', '<=', $request->tanggal_to);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('tanggal', 'desc')->paginate(15)->withQueryString();

        return view('guru.attendances.index', compact('course', 'attendances'));
    }

    public function createAttendance($courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);
        $students = $course->students ?? collect();

        return view('guru.attendances.create', compact('course', 'students'));
    }

    public function storeAttendance(Request $request, $courseId)
    {
        $course = Course::where('guru_id', auth()->id())->findOrFail($courseId);

        $request->validate([
            'tanggal' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:users,id',
            'attendances.*.status' => 'required|in:hadir,izin,sakit,alpa',
            'attendances.*.keterangan' => 'nullable|string',
        ]);

        foreach ($request->attendances as $attendanceData) {
            Attendance::updateOrCreate(
                [
                    'course_id' => $courseId,
                    'student_id' => $attendanceData['student_id'],
                    'tanggal' => $request->tanggal,
                ],
                [
                    'status' => $attendanceData['status'],
                    'keterangan' => $attendanceData['keterangan'] ?? null,
                    'guru_id' => auth()->id(),
                ]
            );
        }

        return redirect()->route('guru.attendances', $courseId)->with('success', 'Absensi berhasil disimpan!');
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
            'status' => 'required|in:hadir,izin,sakit,alpa',
            'keterangan' => 'nullable|string',
        ]);

        $attendance->update([
            'status' => $request->status,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('guru.attendances', $courseId)->with('success', 'Absensi berhasil diperbarui!');
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
        // Support context ujian untuk menambah soal ke ujian
        $contextUjian = null;
        if (request()->filled('ujian_id')) {
            $contextUjian = Ujian::with(['course.guru'])->find(request('ujian_id'));
            if (!$contextUjian) {
                return redirect()->route('guru.bank-soal')->with('error', 'Ujian tidak ditemukan.');
            }
            // Pastikan ujian ini milik guru yang login
            if ($contextUjian->course->guru_id !== auth()->id()) {
                abort(403, 'Anda tidak memiliki akses ke ujian ini.');
            }
        }

        $query = Question::where('guru_id', auth()->id());

        if ($contextUjian) {
            // Saat mode context ujian, tampilkan soal dari course yang sama
            $query->where('course_id', $contextUjian->course_id);
        }

        $questions = $query->with('course')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('guru.bank-soal.index', compact('questions', 'contextUjian'));
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
        // RBAC: pastikan soal ini milik guru yang sedang login
        if ($question->guru_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke soal ini.');
        }
        return view('guru.bank-soal.show', compact('question'));
    }

    public function editBankSoal(Question $question)
    {
        // RBAC: pastikan soal ini milik guru yang sedang login
        if ($question->guru_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke soal ini.');
        }
        $courses = Course::where('guru_id', auth()->id())->get();
        return view('guru.bank-soal.edit', compact('question', 'courses'));
    }

    public function updateBankSoal(Request $request, Question $question)
    {
        // RBAC: pastikan soal ini milik guru yang sedang login
        if ($question->guru_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke soal ini.');
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
        // RBAC: pastikan soal ini milik guru yang sedang login
        if ($question->guru_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke soal ini.');
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
    public function nilaiUjian(Request $request)
    {
        $user = auth()->user();
        $classId = $request->get('class_id');
        $search = $request->get('search');
        $type = $request->get('type');

        // Get classes taught by this guru
        $courseIds = Course::where('guru_id', $user->id)->pluck('id');
        $classIdsFromCourses = Course::where('guru_id', $user->id)->whereNotNull('class_id')->pluck('class_id');
        $classIdsFromSchedules = Schedule::whereIn('course_id', $courseIds)->pluck('class_id');
        $allClassIds = $classIdsFromCourses->concat($classIdsFromSchedules)->unique()->filter();
        $classes = SchoolClass::whereIn('id', $allClassIds)->get();

        $query = UjianResult::whereHas('ujian.course', function($q) use ($user) {
            $q->where('guru_id', $user->id);
        })->with(['ujian.course', 'student']);

        if ($request->filled('class_id')) {
            $query->whereHas('student', function($q) use ($classId) {
                $q->where('class_id', $classId);
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('ujian', function($q) use ($type) {
                $q->where('judul', 'like', '%' . $type . '%');
            });
        }

        $results = $query->orderBy('created_at', 'desc')->get();

        return view('guru.nilai-ujian.index', compact('results', 'classes', 'classId', 'search', 'type'))
            ->with('examResults', $results);
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
            ->get();

        $correctCount = $result->answers()->where('is_correct', true)->count();
        $totalCount = $result->answers()->count();

        return view('guru.nilai-ujian.review', compact('result', 'questions', 'correctCount', 'totalCount'));
    }

    public function updateExamScore(Request $request, $resultId)
    {
        $user = auth()->user();
        
        $result = UjianResult::whereHas('sesiUjian.ujian.course', function($query) use ($user) {
            $query->where('guru_id', $user->id);
        })->findOrFail($resultId);

        $request->validate([
            'score' => 'required|numeric|min:0',
        ]);

        $result->update([
            'score' => $request->score
        ]);

        return redirect()->back()->with('success', 'Skor berhasil diperbarui.');
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

        $pdf = Pdf::loadView('guru.exports.exam_grades_pdf', [
            'results' => $results,
            'examResults' => $results,
        ]);
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

        // Get available students (students in exam classes but not yet enrolled in this session)
        $classIds = $sesi->ujian->class_ids ?? [];
        $enrolledStudentIds = $sesi->students()->pluck('users.id')->toArray();
        
        $availableStudents = User::where('role', 'siswa')
            ->whereIn('class_id', $classIds)
            ->whereNotIn('id', $enrolledStudentIds)
            ->with('schoolClass')
            ->get();

        return view('guru.sesi-ujian.show', compact('sesi', 'availableStudents'));
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
        
        // Tampilkan soal yang sudah di-assign ke ujian ini (ujian_id sesuai)
        $ujianQuestions = Question::where('ujian_id', $ujian->id)
            ->with('course')
            ->latest()
            ->get();

        // Tampilkan soal yang tersedia (belum di-assign, atau dari course/guru yang sama)
        $availableQuestions = Question::where('guru_id', auth()->id())
            ->where('course_id', $ujian->course_id)
            ->where(function($query) use ($ujian) {
                $query->whereNull('ujian_id')
                      ->orWhere('ujian_id', '!=', $ujian->id);
            })
            ->with('course')
            ->latest()
            ->get();

        return view('guru.ujian.add-questions', compact('ujian', 'availableQuestions', 'ujianQuestions'));
    }

    public function storeQuestionsToUjian(Request $request, Ujian $ujian)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        // Verify semua questions milik guru ini
        $questionIds = $request->question_ids;
        $ownedCount = Question::whereIn('id', $questionIds)
            ->where('guru_id', auth()->id())
            ->count();

        if ($ownedCount !== count($questionIds)) {
            abort(403, 'Anda tidak memiliki akses ke semua soal yang dipilih.');
        }

        // Update semua selected questions dengan ujian_id ini
        Question::whereIn('id', $questionIds)
            ->update(['ujian_id' => $ujian->id]);

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', 'Soal berhasil ditambahkan ke ujian.');
    }

    public function removeQuestionFromUjian(Ujian $ujian, Question $question)
    {
        // Check if ujian belongs to this guru's course
        if ($ujian->course->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        // Jika soal ini terkait dengan ujian, hapus relasi dengan set ujian_id ke NULL
        if ($question->ujian_id == $ujian->id) {
            $question->update(['ujian_id' => null]);
        }

        return redirect()->route('guru.ujian.show', $ujian)
            ->with('success', 'Soal berhasil dihapus dari ujian.');
    }

    /**
     * uploadImage — Handler upload gambar untuk Rich Text Editor (Guru).
     *
     * Menerima  : POST multipart/form-data dengan field 'file'
     * Validasi  : hanya image (jpeg/png/jpg/gif/webp), maks 20 MB
     * Simpan ke : public/uploads/soal
     * Return    : JSON { success, file: { name, url } }
     */
    public function uploadSoalImage(Request $request)
    {
        $request->validate([
            // Maksimum 20 MB = 20 * 1024 = 20480 kilobytes
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        try {
            $file     = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            // Simpan ke public/uploads/soal agar bisa diakses langsung via URL
            $destination = public_path('uploads/soal');
            if (! is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            $file->move($destination, $filename);

            $url = asset('uploads/soal/' . $filename);

            return response()->json([
                'success' => true,
                'file'    => [
                    'name' => $filename,
                    'url'  => $url,
                ],
                // Beberapa rich-text editor (mis. Froala, Quill) juga membaca key 'link'
                'link'    => $url,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage(),
            ], 500);
        }
    }
}