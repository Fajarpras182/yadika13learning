<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Assignment;
use App\Models\Grade;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\SesiUjian;
use App\Models\UjianResult;
use App\Models\UjianAnswer;
use App\Models\Question;
use App\Models\SchoolClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaController extends Controller
{
    private function getEnrolledCourseIds($user)
    {
        return Course::where('class_id', $user->class_id)
            ->orWhereHas('schedules', function($query) use ($user) {
                $query->where('class_id', $user->class_id);
            })->pluck('id');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $courseIds = $this->getEnrolledCourseIds($user);
        $courses = Course::whereIn('id', $courseIds)->with(['guru', 'lessons', 'assignments'])->get();
        $enrolledCourses = $courses->count();

        // Get pending assignments (not submitted or not graded)
        $pendingAssignments = Assignment::whereIn('course_id', $courseIds)
        ->whereDoesntHave('grades', function($query) use ($user) {
            $query->where('student_id', $user->id)->where('status', 'sudah_dinilai');
        })
        ->count();

        // Get recent grades (last 10)
        $recentGrades = Grade::where('student_id', $user->id)
            ->with(['assignment.course.guru'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        // Recent courses (all for now, or limit if needed)
        $recentCourses = $courses->take(5); // Take first 5 as recent

        return view('siswa.dashboard', compact('courses', 'enrolledCourses', 'pendingAssignments', 'recentGrades', 'recentCourses'));
    }

    public function courses()
    {
        $user = Auth::user();
        $courseIds = $this->getEnrolledCourseIds($user);
        $courses = Course::whereIn('id', $courseIds)
            ->with(['guru', 'major', 'lessons', 'assignments', 'schedules.schoolClass.major'])
            ->paginate(12);
        return view('siswa.courses.index', compact('courses'));
    }

    public function showCourse($id)
    {
        $user = Auth::user();
        $courseIds = $this->getEnrolledCourseIds($user);
        $course = Course::where('id', $id)
            ->whereIn('id', $courseIds)
            ->with(['guru', 'lessons', 'assignments', 'schedules.schoolClass.major'])
            ->firstOrFail();

        // Get student's grades for assignments in this course
        $grades = Grade::where('student_id', $user->id)
            ->whereHas('assignment', function($query) use ($course) {
                $query->where('course_id', $course->id);
            })
            ->with('assignment')
            ->get();

        return view('siswa.courses.show', compact('course', 'grades'));
    }

    public function showLesson($courseId, $lessonId)
    {
        $user = Auth::user();
        $courseIds = $this->getEnrolledCourseIds($user);
        $course = Course::where('id', $courseId)
            ->whereIn('id', $courseIds)
            ->firstOrFail();

        $lesson = Lesson::where('id', $lessonId)
            ->where('course_id', $courseId)
            ->firstOrFail();

        return view('siswa.lessons.show', compact('course', 'lesson'));
    }

    public function assignments()
    {
        $user = Auth::user();
        $courseIds = $this->getEnrolledCourseIds($user);
        $assignments = Assignment::whereIn('course_id', $courseIds)
        ->with(['course.guru', 'grades' => function($query) use ($user) {
            $query->where('student_id', $user->id);
        }])
        ->orderBy('deadline', 'desc')
        ->paginate(12);

        return view('siswa.assignments.index', compact('assignments'));
    }

    public function showAssignment($id)
    {
        $user = Auth::user();

        $courseIds = $this->getEnrolledCourseIds($user);
        $assignment = Assignment::where('id', $id)
            ->whereIn('course_id', $courseIds)
            ->with(['course.guru', 'course.schoolClass'])
            ->firstOrFail();

        // Get student's grade for this assignment
        $grade = Grade::where('assignment_id', $id)
            ->where('student_id', $user->id)
            ->first();

        return view('siswa.assignments.show', compact('assignment', 'grade'));
    }

    public function submitAssignment(Request $request, $id)
    {
        $user = Auth::user();

        $courseIds = $this->getEnrolledCourseIds($user);
        $assignment = Assignment::where('id', $id)
            ->whereIn('course_id', $courseIds)
            ->firstOrFail();

        // Check if deadline has passed
        if (now()->isAfter($assignment->deadline)) {
            return redirect()->back()->with('error', 'Deadline pengumpulan tugas telah berakhir.');
        }

        // Check if already submitted
        $existingGrade = Grade::where('assignment_id', $id)
            ->where('student_id', $user->id)
            ->first();

        if ($existingGrade && $existingGrade->status == 'sudah_dikumpulkan') {
            return redirect()->back()->with('error', 'Tugas sudah pernah dikumpulkan.');
        }

        $request->validate([
            'jawaban_text' => 'nullable|string',
            'file_jawaban' => 'nullable|file|mimes:pdf,doc,docx,txt,jpg,jpeg,png|max:10240',
        ]);

        $data = [
            'status' => 'sudah_dikumpulkan',
            'submitted_at' => now(),
        ];

        // Handle text answer
        if ($request->filled('jawaban_text')) {
            $data['jawaban_text'] = $request->jawaban_text;
        }

        // Handle file upload
        if ($request->hasFile('file_jawaban')) {
            $file = $request->file('file_jawaban');
            $filename = time() . '_' . $user->id . '_' . $file->getClientOriginalName();
            $file->storeAs('submissions', $filename, 'public');
            $data['file_jawaban'] = 'submissions/' . $filename;
        }

        if ($existingGrade) {
            // Update existing grade
            if (isset($data['file_jawaban']) && $existingGrade->file_jawaban && \Illuminate\Support\Facades\Storage::disk('public')->exists($existingGrade->file_jawaban)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($existingGrade->file_jawaban);
            }
            $existingGrade->update($data);
        } else {
            // Create new grade
            $data['assignment_id'] = $id;
            $data['student_id'] = $user->id;
            Grade::create($data);
        }

        return redirect()->back()->with('success', 'Tugas berhasil dikumpulkan.');
    }

    public function grades()
    {
        $user = Auth::user();
        $grades = Grade::where('student_id', $user->id)
            ->with(['assignment.course.guru'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('siswa.grades.index', compact('grades'));
    }

    public function profile()
    {
        $siswa = auth()->user();
        return view('siswa.profile', compact('siswa'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'no_hp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        // Check current password if changing password
        if ($request->filled('current_password')) {
            if (!\Illuminate\Support\Facades\Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
            }
        }

        $updateData = $request->only(['name', 'email', 'no_hp', 'alamat']);

        if ($request->filled('password')) {
            $updateData['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function attendances()
    {
        $user = Auth::user();
        $attendances = \App\Models\Attendance::where('student_id', $user->id)
            ->with(['course'])
            ->orderBy('tanggal', 'desc')
            ->paginate(12);

        return view('siswa.attendances.index', compact('attendances'));
    }

    public function schedule()
    {
        $user = Auth::user();

        $courseIds = $this->getEnrolledCourseIds($user);
        // Get all schedules for courses the student is enrolled in
        $schedules = \App\Models\Schedule::whereIn('course_id', $courseIds)
        ->with(['course.guru', 'course.schoolClass.major'])
        ->orderBy('day')
        ->orderBy('start_time')
        ->get();

        // Group schedules by day
        $daysMap = [
            'Senin' => 'Monday',
            'Selasa' => 'Tuesday',
            'Rabu' => 'Wednesday',
            'Kamis' => 'Thursday',
            'Jumat' => 'Friday',
            'Sabtu' => 'Saturday',
            'Minggu' => 'Sunday'
        ];

        $groupedSchedules = [];
        foreach ($schedules as $schedule) {
            $day = $schedule->day;
            if (!isset($groupedSchedules[$day])) {
                $groupedSchedules[$day] = [];
            }
            $groupedSchedules[$day][] = $schedule;
        }

        return view('siswa.schedule', compact('groupedSchedules', 'daysMap'));
    }

    public function forum()
    {
        $messages = \App\Models\Message::whereNull('parent_id')
            ->with(['user', 'replies.user'])
            ->latest()
            ->paginate(20);

        if (request()->ajax()) {
            return view('siswa.messages._list', compact('messages'));
        }

        return view('siswa.messages.index', compact('messages'));
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:messages,id',
        ]);

        \App\Models\Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => auth()->id(),
            'subject' => 'Forum',
            'message' => $request->message,
            'content' => $request->message,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->back()->with('success', 'Pesan berhasil dikirim.');
    }

    public function markMessageAsRead($messageId)
    {
        $message = \App\Models\Message::where('receiver_id', Auth::id())->findOrFail($messageId);
        $message->markAsRead();

        return redirect()->back()->with('success', 'Pesan telah ditandai sebagai dibaca.');
    }

    public function editMessage($messageId)
    {
        $message = \App\Models\Message::where('id', $messageId)->where('sender_id', Auth::id())->firstOrFail();

        return view('siswa.messages.edit', compact('message'));
    }

    public function updateMessage(Request $request, $messageId)
    {
        $message = \App\Models\Message::where('id', $messageId)->where('sender_id', Auth::id())->firstOrFail();

        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
        ];

        // Handle file updates for replies (if any)
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
                Storage::disk('public')->delete($message->file_path);
            }

            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('messages', $filename, 'public');
            $data['file_path'] = 'messages/' . $filename;
        }

        $message->update($data);

        return redirect()->route('siswa.forum')->with('success', 'Pesan berhasil diperbarui.');
    }

    public function deleteMessage($messageId)
    {
        $message = \App\Models\Message::where('id', $messageId)->where('sender_id', Auth::id())->firstOrFail();

        // Delete associated file if exists
        if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
            Storage::disk('public')->delete($message->file_path);
        }

        $message->delete();

        return redirect()->route('siswa.forum')->with('success', 'Pesan berhasil dihapus.');
    }

    public function announcements()
    {
        return view('siswa.announcements');
    }

    public function ujian()
    {
        $user = Auth::user();
        $currentTime = Carbon::now('Asia/Jakarta');

        $sessions = SesiUjian::where('is_active', true)
            ->where(function ($query) use ($user) {
                $query->whereHas('students', function ($q) use ($user) {
                    $q->where('student_id', $user->id);
                })
                ->orWhereHas('ujian', function ($q) use ($user) {
                    $q->whereJsonContains('class_ids', $user->class_id);
                });
            })
            ->with(['ujian.course.guru', 'ujianResults' => function ($q) use ($user) {
                $q->where('student_id', $user->id);
            }])
            ->orderBy('waktu_mulai')
            ->get();

        return view('siswa.ujian.index', compact('sessions', 'currentTime'));
    }

    public function downloadFile($type, $filename)
    {
        $user = Auth::user();

        // Validate type
        if (!in_array($type, ['lessons', 'assignments'])) {
            abort(404);
        }

        // Check if user has access to this file based on their class
        $path = $type . '/' . $filename;

        // Verify the file exists
        if (!Storage::disk('public')->exists($path)) {
            abort(404);
        }

        // For lessons, check if the lesson belongs to a course in the student's class
        if ($type === 'lessons') {
            $lesson = Lesson::where('file_materi', $path)->first();
            if ($lesson) {
                $courseIds = $this->getEnrolledCourseIds($user);
                if (!$courseIds->contains($lesson->course_id)) {
                    abort(403);
                }
            }
        }

        // For assignments, check if the assignment belongs to a course in the student's class
        if ($type === 'assignments') {
            $assignment = Assignment::where('file_path', $path)->first();
            if ($assignment) {
                $courseIds = $this->getEnrolledCourseIds($user);
                if (!$courseIds->contains($assignment->course_id)) {
                    abort(403);
                }
            }
        }



        return Storage::disk('public')->download($path, basename($path), [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . basename($path) . '"'
        ]);
    }

    public function startUjian(SesiUjian $sesi)
    {
        $user = Auth::user();

        // Validate time
        $now = Carbon::now('Asia/Jakarta');
        $mulai = $sesi->waktu_mulai?->timezone('Asia/Jakarta');
        $selesai = $sesi->waktu_selesai?->timezone('Asia/Jakarta');

        if ($now->lt($mulai)) {
            return redirect()->route('siswa.ujian')->with('error', 'Ujian belum dimulai. Silakan tunggu waktu mulai.');
        }
        if ($now->gt($selesai)) {
            return redirect()->route('siswa.ujian')->with('error', 'Waktu ujian sudah selesai.');
        }

        // Validate eligibility (class_id in ujian.class_ids)
        $classIds = is_array($sesi->ujian->class_ids) ? $sesi->ujian->class_ids : json_decode($sesi->ujian->class_ids, true) ?? [];
        if (!in_array($user->class_id, $classIds)) {
            return redirect()->route('siswa.ujian')->with('error', 'Anda tidak berhak mengikuti ujian ini.');
        }

        // Check if already submitted
        $existingResult = UjianResult::where('sesi_ujian_id', $sesi->id)
            ->where('student_id', $user->id)
            ->where('status', 'completed')
            ->first();
        if ($existingResult) {
            return redirect()->route('siswa.ujian')->with('error', 'Ujian sudah selesai dikerjakan.');
        }

        // Get or create result
        $result = UjianResult::firstOrCreate(
            [
                'sesi_ujian_id' => $sesi->id,
                'student_id' => $user->id,
            ],
            [
                'start_time' => $now,
                'status' => 'in_progress',
            ]
        );

        // Load questions (random if soal_acak)
        $questionsQuery = $sesi->ujian->questions()->where('is_active', true);
        if ($sesi->ujian->soal_acak) {
            $questions = $questionsQuery->inRandomOrder()->get();
        } else {
            $questions = $questionsQuery->orderBy('id')->get();
        }

        $result->load('ujian');
        $durasi = $sesi->ujian->durasi_menit ?? 60; // default 60 min
        $savedAnswers = $result->answers()->pluck('selected_answer', 'question_id')->toArray();

        return view('siswa.ujian.exam', compact('sesi', 'result', 'questions', 'durasi', 'savedAnswers'));
    }

    public function submitUjian(Request $request, SesiUjian $sesi)
    {
        $user = Auth::user();
        $now = Carbon::now('Asia/Jakarta');

        $result = UjianResult::where('sesi_ujian_id', $sesi->id)
            ->where('student_id', $user->id)
            ->lockForUpdate()
            ->firstOrFail();

        if ($result->status !== 'in_progress') {
            return response()->json([
                'success' => false,
                'message' => 'Ujian sudah diselesaikan atau tidak aktif.',
                'redirectUrl' => route('siswa.ujian')
            ], 400);
        }

        $request->validate([
            'answers' => 'required|array',
        ]);

        DB::transaction(function () use ($result, $request, $now, $sesi) {
            $questions = $sesi->ujian->questions()->where('is_active', true)->get();
            $totalScore = 0;
            $totalQuestions = $questions->count();

            // Guard against division by zero
            $pointPerQuestion = ($totalQuestions > 0) ? ($sesi->ujian->bobot_nilai / $totalQuestions) : 0;

            foreach ($questions as $question) {
                // Get answer from request, or fallback to existing answer (from auto-save)
                $selectedAnswer = $request->answers[$question->id] ?? null;
                
                if ($selectedAnswer === null) {
                    $existingAnswer = UjianAnswer::where('ujian_result_id', $result->id)
                        ->where('question_id', $question->id)
                        ->first();
                    $selectedAnswer = $existingAnswer ? $existingAnswer->selected_answer : null;
                }

                $answerData = [
                    'ujian_result_id' => $result->id,
                    'question_id' => $question->id,
                    'selected_answer' => $selectedAnswer,
                ];

                // Determine correct - trim and case-insensitive comparison
                $selected = trim(strtolower((string)($selectedAnswer ?? '')));
                $correct = trim(strtolower((string)($question->kunci_jawaban ?? '')));
                $isCorrect = ($selected !== '') && ($selected === $correct);
                
                $answerData['is_correct'] = $isCorrect;
                $answerData['points'] = $isCorrect ? $pointPerQuestion : 0;
                $totalScore += $answerData['points'];

                UjianAnswer::updateOrCreate(
                    [
                        'ujian_result_id' => $result->id,
                        'question_id' => $question->id,
                    ],
                    $answerData
                );
            }

            // Update result
            $minutesTaken = $result->start_time ? $result->start_time->diffInMinutes($now, false) : 0;
            $result->update([
                'end_time' => $now,
                'time_taken_minutes' => $minutesTaken,
                'score' => $totalScore,
                'total_questions' => $totalQuestions,
                'status' => 'completed',
            ]);
        });

        $result = $result->fresh();

        // Check if should display results and return PDF URL
        if ($sesi->ujian->tampilkan_hasil) {
            $pdfUrl = route('siswa.ujian.result-pdf', $result->id);
            return response()->json([
                'success' => true,
                'message' => 'Ujian berhasil diselesaikan! Skor: ' . number_format($result->score, 2),
                'score' => $result->score,
                'pdfUrl' => $pdfUrl,
                'shouldDownloadPdf' => true,
                'redirectUrl' => route('siswa.ujian')
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ujian berhasil diselesaikan! Skor: ' . number_format($result->score, 2),
            'score' => $result->score,
            'shouldDownloadPdf' => false,
            'redirectUrl' => route('siswa.ujian')
        ]);
    }

    public function downloadResultPdf(UjianResult $result)
    {
        // Check authorization
        if ($result->student_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $sesi = $result->sesiUjian;
        $user = Auth::user();

        if (!$sesi || !$sesi->ujian) {
            abort(404, 'Ujian data not found');
        }

        // Load all answers with question details
        $answers = $result->answers()
            ->with('question')
            ->get();

        // Calculate percentage score - Guard against division by zero
        $bobotNilai = $sesi->ujian->bobot_nilai ?: 100; // Use 100 if zero to avoid crash
        $percentageScore = ($result->score / $bobotNilai) * 100;
        $percentageScore = round($percentageScore, 2);

        // Recalculate correct count by comparing answers with question's kunci_jawaban
        // This ensures accuracy regardless of stored is_correct value
        $correctCount = 0;
        foreach ($answers as $answer) {
            if ($answer->question) {
                $selectedAnswer = trim(strtolower((string)($answer->selected_answer ?? '')));
                $correctAnswer = trim(strtolower((string)($answer->question->kunci_jawaban ?? '')));
                if ($selectedAnswer !== '' && $selectedAnswer === $correctAnswer) {
                    $correctCount++;
                }
            }
        }

        $data = [
            'result' => $result,
            'sesi' => $sesi,
            'user' => $user,
            'answers' => $answers,
            'percentageScore' => $percentageScore,
            'correctCount' => $correctCount,
            'totalQuestions' => $result->total_questions ?: $answers->count(),
        ];

        $pdf = Pdf::loadView('pdf.hasil-ujian', $data);
        return $pdf->download('hasil-ujian-' . \Illuminate\Support\Str::slug($sesi->ujian->judul) . '-' . now()->format('Y-m-d-His') . '.pdf');
    }

}


