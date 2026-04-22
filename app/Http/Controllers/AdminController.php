<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Course;
use App\Models\Question;
use App\Models\SesiUjian;
use App\Models\Ujian;
use App\Services\BankSoalDocumentImport;
use App\Models\SchoolClass;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function dashboard()
    {
$totalUsers   = User::count();
        $totalGuru    = User::where('role', 'guru')->count();
        $totalSiswa   = User::where('role', 'siswa')->count();
        $totalCourses = Schema::hasTable('courses') ? Course::count() : 0;
        $pendingUsers = User::where('is_active', false)->count();

        return view('admin.dashboard', compact(
            'totalUsers','totalGuru','totalSiswa','totalCourses','pendingUsers'
        ));
    }

    /* ====================== PROFILE ======================== */
    public function profile()
    {
        return view('admin.profile');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'nis_nip'           => 'required|string|max:20',
            'no_hp'             => 'nullable|string|max:15',
            'alamat'            => 'nullable|string',
            'current_password'  => 'nullable|string',
            'password'          => 'nullable|string|min:8|confirmed',
        ]);

        if ($request->filled('password') && !\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama tidak sesuai.']);
        }

        $user->update([
            'name'      => $request->name,
            'email'     => $request->email,
            'nis_nip'   => $request->nis_nip,
            'no_hp'     => $request->no_hp,
            'alamat'    => $request->alamat,
            'password'  => $request->filled('password') ? bcrypt($request->password) : $user->password,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /* ====================== USERS ======================== */
    public function users()
    {
        $query = User::query();
        if (request('q')) {
            $q = request('q');
            $query->where(function ($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('nis_nip','like',"%$q%");
            });
        }
        if (request('role')) $query->where('role', request('role'));
        if (request()->filled('status')) $query->where('is_active', request('status') === 'active');

        $users = $query->latest()->paginate(10)->withQueryString();
        return view('admin.users.index', compact('users'));
    }

    public function createUser() { return view('admin.users.create'); }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role'     => 'required|in:admin,guru,siswa',
            'nis_nip'  => 'required|string|max:20',
            'kelas'    => 'nullable|string|max:10',
            'jurusan'  => 'nullable|string|max:50',
            'no_hp'    => 'nullable|string|max:15',
            'alamat'   => 'nullable|string',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => $request->role,
            'nis_nip'  => $request->nis_nip,
            'kelas'    => $request->kelas,
            'jurusan'  => $request->jurusan,
            'no_hp'    => $request->no_hp,
            'alamat'   => $request->alamat,
            'is_active' => true,
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil dibuat.');
    }

    public function activateUser($id)
    {
        User::findOrFail($id)->update(['is_active' => true]);
        return back()->with('success', 'User berhasil diaktifkan.');
    }

    public function deactivateUser($id)
    {
        User::findOrFail($id)->update(['is_active' => false]);
        return back()->with('success', 'User berhasil dinonaktifkan.');
    }

    private function filteredUsersCollection()
    {
        $query = User::query();
        if (request('q')) {
            $q = request('q');
            $query->where(function ($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('nis_nip','like',"%$q%");
            });
        }
        if (request('role')) $query->where('role', request('role'));
        if (request()->filled('status')) $query->where('is_active', request('status') === 'active');
        return $query->orderBy('name')->get();
    }

    /* ====================== COURSES ======================== */
    public function courses()
    {
        $query = Course::with('guru');
        if (request('q')) {
            $q = request('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama_mata_pelajaran', 'like', "%$q%")
                  ->orWhere('kode_mata_pelajaran','like',"%$q%")
                  ->orWhere('deskripsi','like',"%$q%");
            });
        }

        if (request('kelas')) $query->whereHas('schoolClass', function($q) {
            $q->where('name', request('kelas'));
        });
        if (request('jurusan')) $query->whereHas('major', function($q) {
            $q->where('name', request('jurusan'));
        });
        if (request()->filled('status')) $query->where('is_active', request('status') === 'active');
        if (request('guru_id')) $query->where('guru_id', request('guru_id'));

        $courses = $query->latest()->paginate(10)->withQueryString();
        $gurus = User::where('role', 'guru')->orderBy('name')->get();

        return view('admin.courses.index', compact('courses','gurus'));
    }

    public function createCourse()
    {
        $gurus = User::where('role', 'guru')->orderBy('name')->get();
        $classes = \App\Models\SchoolClass::where('is_active', true)->orderBy('name')->get();
        $majors = \App\Models\Major::where('is_active', true)->orderBy('name')->get();
        return view('admin.courses.create', compact('gurus', 'classes', 'majors'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'kode_mata_pelajaran' => 'required|string|max:10|unique:courses',
            'nama_mata_pelajaran' => 'required|string|max:255',
            'guru_id'             => 'required|exists:users,id',
            'class_id'            => 'required|exists:classes,id',
            'major_id'            => 'required|exists:majors,id',
            'semester'            => 'required|in:Ganjil,Genap',
            'is_active'           => 'boolean',
        ]);

        Course::create([
            'kode_mata_pelajaran' => $request->kode_mata_pelajaran,
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id'             => $request->guru_id,
            'class_id'            => $request->class_id,
            'major_id'            => $request->major_id,
            'semester'            => $request->semester,
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.courses')->with('success', 'Mata pelajaran berhasil dibuat.');
    }

    public function editCourse($id)
    {
        $course = Course::findOrFail($id);
        $gurus = User::where('role', 'guru')->orderBy('name')->get();
        $classes = \App\Models\SchoolClass::where('is_active', true)->orderBy('name')->get();
        $majors = \App\Models\Major::where('is_active', true)->orderBy('name')->get();
        return view('admin.courses.edit', compact('course', 'gurus', 'classes', 'majors'));
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $request->validate([
            'kode_mata_pelajaran' => 'required|string|max:10|unique:courses,kode_mata_pelajaran,' . $id,
            'nama_mata_pelajaran' => 'required|string|max:255',
            'guru_id'             => 'required|exists:users,id',
            'class_id'            => 'required|exists:classes,id',
            'major_id'            => 'required|exists:majors,id',
            'semester'            => 'required|in:Ganjil,Genap',
            'is_active'           => 'boolean',
        ]);

        $course->update([
            'kode_mata_pelajaran' => $request->kode_mata_pelajaran,
            'nama_mata_pelajaran' => $request->nama_mata_pelajaran,
            'guru_id'             => $request->guru_id,
            'class_id'            => $request->class_id,
            'major_id'            => $request->major_id,
            'semester'            => $request->semester,
            'is_active'           => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.courses')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroyCourse($id)
    {
        Course::findOrFail($id)->delete();
        return redirect()->route('admin.courses')->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    private function filteredCoursesCollection()
    {
        $query = Course::with('guru', 'schoolClass', 'major');
        if (request('q')) {
            $q = request('q');
            $query->where(function ($w) use ($q) {
                $w->where('nama_mata_pelajaran','like',"%$q%")
                  ->orWhere('kode_mata_pelajaran','like',"%$q%")
                  ->orWhere('deskripsi','like',"%$q%");
            });
        }

        if (request('kelas')) $query->whereHas('schoolClass', function($q) {
            $q->where('name', request('kelas'));
        });
        if (request('jurusan')) $query->whereHas('major', function($q) {
            $q->where('name', request('jurusan'));
        });
        if (request()->filled('status')) $query->where('is_active', request('status') === 'active');
        if (request('guru_id')) $query->where('guru_id', request('guru_id'));

        return $query->orderBy('nama_mata_pelajaran')->get();
    }

    /* ====================== UJIAN MANAGEMENT ======================== */

    public function ujian()
    {
        $exams = Ujian::with('course')
                     ->withCount('questions')
                     ->orderBy('tanggal_ujian', 'desc')
                     ->get();
        
        return view('admin.ujian.index', compact('exams'));
    }

    public function createUjian()
    {
        $courses = Course::all();
        $classes = SchoolClass::all();
        $defaultTanggalUjian = now()->addDays(7)->format('Y-m-d\TH:i');

        return view('admin.ujian.create', compact('courses', 'classes', 'defaultTanggalUjian'));
    }

    public function storeUjian(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:classes,id',
            'tanggal_ujian' => 'required|date|after:now',
            'durasi_menit' => 'required|integer|min:1',
            'bobot_nilai' => 'required|integer|min:1|max:100',
            'soal_acak' => 'required|boolean',
            'jawaban_acak' => 'required|boolean',
            'tampilkan_hasil' => 'required|boolean',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // For admin, get guru_id from request or use admin's id
        $guruId = $request->guru_id ?? Auth::id();

        Ujian::create([
            'judul' => $request->judul,
            'course_id' => $request->course_id,
            'guru_id' => $guruId,
            'class_ids' => $request->class_ids,
            'tanggal_ujian' => $request->tanggal_ujian,
            'durasi_menit' => $request->durasi_menit,
            'bobot_nilai' => $request->bobot_nilai,
            'soal_acak' => $request->soal_acak,
            'jawaban_acak' => $request->jawaban_acak,
            'tampilkan_hasil' => $request->tampilkan_hasil,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.ujian')->with('success', 'Ujian berhasil dibuat.');
    }

    public function showUjian(Ujian $ujian)
    {
        $classIds = is_array($ujian->class_ids) ? $ujian->class_ids : json_decode($ujian->class_ids ?? '[]', true) ?? [];
        $classes = SchoolClass::whereIn('id', $classIds)->get();
        return view('admin.ujian.show', compact('ujian', 'classes'));
    }

    public function editUjian(Ujian $ujian)
    {
        $courses = Course::all();
        $classes = SchoolClass::all();
        return view('admin.ujian.edit', compact('ujian', 'courses', 'classes'));
    }

    public function updateUjian(Request $request, Ujian $ujian)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'class_ids' => 'required|array|min:1',
            'class_ids.*' => 'exists:classes,id',
            'tanggal_ujian' => 'required|date|after:now',
            'durasi_menit' => 'required|integer|min:1',
            'bobot_nilai' => 'required|integer|min:1|max:100',
            'soal_acak' => 'required|boolean',
            'jawaban_acak' => 'required|boolean',
            'tampilkan_hasil' => 'required|boolean',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $ujian->update([
            'judul' => $request->judul,
            'course_id' => $request->course_id,
            'class_ids' => $request->class_ids,
            'tanggal_ujian' => $request->tanggal_ujian,
            'durasi_menit' => $request->durasi_menit,
            'bobot_nilai' => $request->bobot_nilai,
            'soal_acak' => $request->soal_acak,
            'jawaban_acak' => $request->jawaban_acak,
            'tampilkan_hasil' => $request->tampilkan_hasil,
            'deskripsi' => $request->deskripsi,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.ujian')->with('success', 'Ujian berhasil diperbarui.');
    }

    public function deleteUjian(Ujian $ujian)
    {
        if ($ujian->questions->count() > 0) {
            return redirect()->route('admin.ujian')->with('error', 'Tidak dapat menghapus ujian yang masih memiliki soal. Hapus soal terlebih dahulu.');
        }

        $ujian->delete();
        return redirect()->route('admin.ujian')->with('success', 'Ujian berhasil dihapus.');
    }

    public function sesiUjian()
    {
        $sessions = SesiUjian::with(['ujian.course', 'students'])->orderBy('waktu_mulai', 'desc')->paginate(10);
        return view('admin.sesi-ujian.index', compact('sessions'));
    }

    public function createSesiUjian()
    {
        $ujians = Ujian::with('course')->orderBy('judul')->get();
        return view('admin.sesi-ujian.create', compact('ujians'));
    }

    public function storeSesiUjian(Request $request)
    {
        $request->validate([
            'ujian_id' => 'required|exists:ujians,id',
            'nama_sesi' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'is_active' => 'boolean',
        ]);

        // Parse waktu dari form sebagai timezone Jakarta, kemudian convert ke UTC untuk penyimpanan
        $waktuMulai = Carbon::createFromFormat('Y-m-d\TH:i', $request->waktu_mulai, 'Asia/Jakarta')->setTimezone('UTC');
        $waktuSelesai = Carbon::createFromFormat('Y-m-d\TH:i', $request->waktu_selesai, 'Asia/Jakarta')->setTimezone('UTC');

        SesiUjian::create([
            'ujian_id' => $request->ujian_id,
            'nama_sesi' => $request->nama_sesi,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.sesi-ujian')->with('success', 'Sesi ujian berhasil dibuat.');
    }

    public function showSesiUjian(SesiUjian $sesi)
    {
        $sesi->load(['ujian.course', 'students.schoolClass', 'students.ujianResults']);
        
        // Get available students from the exam classes
        $classIds = is_array($sesi->ujian->class_ids) ? $sesi->ujian->class_ids : json_decode($sesi->ujian->class_ids ?? '[]', true) ?? [];
        $availableStudents = collect();
        if (!empty($classIds)) {
            $availableStudents = User::where('role', 'siswa')
                ->whereIn('class_id', $classIds)
                ->whereDoesntHave('sesiUjians', function($query) use ($sesi) {
                    $query->where('sesi_ujian_id', $sesi->id);
                })
                ->with('schoolClass')
                ->orderBy('name')
                ->get();
        }
        
        return view('admin.sesi-ujian.show', compact('sesi', 'availableStudents'));
    }

    public function editSesiUjian(SesiUjian $sesi)
    {
        $ujians = Ujian::with('course')->orderBy('judul')->get();
        return view('admin.sesi-ujian.edit', compact('sesi', 'ujians'));
    }

    public function updateSesiUjian(Request $request, SesiUjian $sesi)
    {
        $request->validate([
            'ujian_id' => 'required|exists:ujians,id',
            'nama_sesi' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
            'is_active' => 'boolean',
        ]);

        // Parse waktu dari form sebagai timezone Jakarta, kemudian convert ke UTC untuk penyimpanan
        $waktuMulai = Carbon::createFromFormat('Y-m-d\TH:i', $request->waktu_mulai, 'Asia/Jakarta')->setTimezone('UTC');
        $waktuSelesai = Carbon::createFromFormat('Y-m-d\TH:i', $request->waktu_selesai, 'Asia/Jakarta')->setTimezone('UTC');

        $sesi->update([
            'ujian_id' => $request->ujian_id,
            'nama_sesi' => $request->nama_sesi,
            'waktu_mulai' => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.sesi-ujian')->with('success', 'Sesi ujian berhasil diperbarui.');
    }

    public function deleteSesiUjian(SesiUjian $sesi)
    {
        $sesi->delete();
        return redirect()->route('admin.sesi-ujian')->with('success', 'Sesi ujian berhasil dihapus.');
    }

    public function storeSesiUjianStudent(Request $request, SesiUjian $sesi)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
        ]);

        // Check if student is already assigned
        if ($sesi->students()->where('student_id', $request->student_id)->exists()) {
            return redirect()->back()->with('error', 'Student is already assigned to this exam session');
        }

        $sesi->students()->attach($request->student_id);

        return redirect()->back()->with('success', 'Student assigned successfully');
    }

    public function bulkAssignSesiUjianStudents(Request $request, SesiUjian $sesi)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        foreach ($request->student_ids as $studentId) {
            if (!$sesi->students()->where('student_id', $studentId)->exists()) {
                $sesi->students()->attach($studentId);
            }
        }

        return redirect()->back()->with('success', 'Students assigned successfully');
    }

    public function destroySesiUjianStudent(SesiUjian $sesi, $studentId)
    {
        $sesi->students()->detach($studentId);
        return redirect()->back()->with('success', 'Student removed from exam session');
    }

    public function addQuestionsToUjian(Ujian $ujian)
    {
        $availableQuestions = Question::where('course_id', $ujian->course_id)
            ->whereNull('ujian_id')
            ->where('is_active', true)
            ->get();

        $ujianQuestions = $ujian->questions;
        return view('admin.ujian.add-questions', compact('ujian', 'availableQuestions', 'ujianQuestions'));
    }

    public function storeQuestionsToUjian(Request $request, Ujian $ujian)
    {
        $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $questions = Question::whereIn('id', $request->question_ids)
            ->where('course_id', $ujian->course_id)
            ->whereNull('ujian_id')
            ->where('is_active', true)
            ->get();

        if ($questions->count() !== count($request->question_ids)) {
            return redirect()->back()->with('error', 'Beberapa soal tidak valid atau sudah digunakan.');
        }

        Question::whereIn('id', $request->question_ids)->update(['ujian_id' => $ujian->id]);

        return redirect()->route('admin.ujian.show', $ujian->id)
            ->with('success', 'Berhasil menambahkan ' . $questions->count() . ' soal ke ujian.');
    }

    public function removeQuestionFromUjian(Ujian $ujian, Question $question)
    {
        if ($question->ujian_id !== $ujian->id) {
            return response()->json(['success' => false, 'message' => 'Soal tidak ditemukan di ujian ini.'], 404);
        }

        $question->update(['ujian_id' => null]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Soal berhasil dihapus dari ujian.'
            ]);
        }

        return redirect()->route('admin.ujian.add-questions', $ujian->id)
            ->with('success', 'Soal berhasil dihapus dari ujian.');
    }

    /* ====================== BANK SOAL MANAGEMENT ======================== */

    public function bankSoal()
    {
        $contextUjian = null;
        if (request()->filled('ujian_id')) {
            $contextUjian = Ujian::with(['course.guru'])->find(request('ujian_id'));
            if (! $contextUjian) {
                return redirect()->route('admin.bank-soal')->with('error', 'Ujian tidak ditemukan.');
            }
        }

        $query = Question::query();

        if ($contextUjian) {
            $query->where('course_id', $contextUjian->course_id)
                ->where(function ($q) use ($contextUjian) {
                    $q->whereNull('ujian_id')
                        ->orWhere('ujian_id', $contextUjian->id);
                });
        } else {
            if (request('course_id')) {
                $query->where('course_id', request('course_id'));
            }
            if (request('guru_id')) {
                $query->whereHas('course', function ($q) {
                    $q->where('guru_id', request('guru_id'));
                });
            }
            if (request()->filled('status')) {
                $is_active = request('status') === 'active' ? 1 : 0;
                $query->where('is_active', $is_active);
            }
        }

        $questions = $query->with(['course.guru'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.bank-soal.index', compact('questions', 'contextUjian'));
    }

    public function createBankSoal(Request $request)
    {
$gurus = User::where('role', 'guru')->get(['id', 'name']);
        $courses = Course::all();
        $contextUjian = null;
        if ($request->filled('ujian_id')) {
            $contextUjian = Ujian::with(['course.guru'])->find($request->ujian_id);
        }

        return view('admin.bank-soal.create', compact('gurus', 'courses', 'contextUjian'));
    }

    public function storeBankSoal(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'ujian_id' => 'nullable|exists:ujians,id',
            'pertanyaan' => 'required|string',
            'jawaban_a' => 'required|string',
            'jawaban_b' => 'required|string',
            'jawaban_c' => 'required|string',
            'jawaban_d' => 'required|string',
            'jawaban_e' => 'required|string',
            'kunci_jawaban' => 'required|in:a,b,c,d,e',
        ]);

        $ujianId = null;
        if ($request->filled('ujian_id')) {
            $ujian = Ujian::query()->find($request->ujian_id);
            if (! $ujian || (int) $ujian->course_id !== (int) $request->course_id) {
                return redirect()->back()->withInput()->with('error', 'Ujian tidak sesuai dengan mata pelajaran yang dipilih.');
            }
            $ujianId = (int) $ujian->id;
        }

        Question::create([
            'ujian_id' => $ujianId,
            'course_id' => $request->course_id,
            'pertanyaan' => $request->pertanyaan,
            'jawaban_a' => $request->jawaban_a,
            'jawaban_b' => $request->jawaban_b,
            'jawaban_c' => $request->jawaban_c,
            'jawaban_d' => $request->jawaban_d,
            'jawaban_e' => $request->jawaban_e,
            'kunci_jawaban' => strtolower($request->kunci_jawaban),
            'is_active' => true,
        ]);

        $q = array_filter([
            'ujian_id' => $request->input('ujian_id'),
            'course_id' => $request->input('course_id'),
        ], fn ($v) => $v !== null && $v !== '');

        return redirect()->route('admin.bank-soal', $q)->with('success', 'Soal berhasil disimpan.');
    }

    public function showBankSoal(Question $question)
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'question' => $question->load('course.guru'),
            ]);
        }

        return redirect()->route('admin.bank-soal.edit', $question->id);
    }

    public function editBankSoal(Question $question)
    {
        if ($question->ujian_id !== null) {
            return redirect()->route('admin.bank-soal')->with('error', 'Soal yang sudah digunakan dalam ujian tidak dapat diedit.');
        }

        $courses = Course::all();
        return view('admin.bank-soal.edit', compact('question', 'courses'));
    }

    public function updateBankSoal(Request $request, Question $question)
    {
        if ($question->ujian_id !== null) {
            return redirect()->route('admin.bank-soal')->with('error', 'Soal yang sudah digunakan dalam ujian tidak dapat diedit.');
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'pertanyaan' => 'required|string',
            'jawaban_a' => 'required|string',
            'jawaban_b' => 'required|string',
            'jawaban_c' => 'required|string',
            'jawaban_d' => 'required|string',
            'jawaban_e' => 'required|string',
            'kunci_jawaban' => 'required|in:a,b,c,d,e',
            'is_active' => 'boolean',
        ]);

        $question->update([
            'course_id' => $request->course_id,
            'pertanyaan' => $request->pertanyaan,
            'jawaban_a' => $request->jawaban_a,
            'jawaban_b' => $request->jawaban_b,
            'jawaban_c' => $request->jawaban_c,
            'jawaban_d' => $request->jawaban_d,
            'jawaban_e' => $request->jawaban_e,
            'kunci_jawaban' => $request->kunci_jawaban,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.bank-soal')->with('success', 'Soal berhasil diperbarui.');
    }

    public function deleteBankSoal(Question $question)
    {
        if ($question->ujian_id !== null) {
            return redirect()->route('admin.bank-soal')->with('error', 'Soal yang sudah digunakan dalam ujian tidak dapat dihapus.');
        }

        $question->delete();
        return redirect()->route('admin.bank-soal')->with('success', 'Soal berhasil dihapus.');
    }

    public function uploadBankSoal(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:15360',
            'course_id' => 'required|exists:courses,id',
            'ujian_id' => 'nullable|exists:ujians,id',
        ]);

        $ext = strtolower($request->file('file')->getClientOriginalExtension());
        $allowedParse = ['csv', 'xls', 'xlsx', 'docx'];
        $allowedUpload = array_merge($allowedParse, ['pdf', 'ppt', 'pptx', 'doc']);

        if (! in_array($ext, $allowedUpload, true)) {
            return redirect()->back()->with('error', 'Ekstensi file tidak didukung. ' . BankSoalDocumentImport::supportedExtensionsMessage());
        }

        if (! in_array($ext, $allowedParse, true)) {
            return redirect()->back()->with('error', BankSoalDocumentImport::supportedExtensionsMessage());
        }

        $ujianId = null;
        if ($request->filled('ujian_id')) {
            $ujian = Ujian::query()->find($request->ujian_id);
            if (! $ujian || (int) $ujian->course_id !== (int) $request->course_id) {
                return redirect()->back()->with('error', 'Ujian tidak sesuai dengan mata pelajaran.');
            }
            $ujianId = (int) $ujian->id;
        }

        try {
            $data = BankSoalDocumentImport::rowsFromUpload($request->file('file'));
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $imported = 0;
        $errors = [];

        foreach ($data as $index => $row) {
            try {
                if (empty($row['pertanyaan']) || empty($row['jawaban_a']) || empty($row['jawaban_b']) ||
                    empty($row['jawaban_c']) || empty($row['jawaban_d']) || empty($row['jawaban_e']) ||
                    empty($row['kunci_jawaban'])) {
                    $errors[] = 'Baris ' . ($index + 2) . ': Data tidak lengkap';

                    continue;
                }

                if (! in_array(strtolower($row['kunci_jawaban']), ['a', 'b', 'c', 'd', 'e'], true)) {
                    $errors[] = 'Baris ' . ($index + 2) . ': Kunci jawaban harus a, b, c, d, atau e';

                    continue;
                }

                Question::create([
                    'ujian_id' => $ujianId,
                    'course_id' => $request->course_id,
                    'pertanyaan' => $row['pertanyaan'],
                    'jawaban_a' => $row['jawaban_a'],
                    'jawaban_b' => $row['jawaban_b'],
                    'jawaban_c' => $row['jawaban_c'],
                    'jawaban_d' => $row['jawaban_d'],
                    'jawaban_e' => $row['jawaban_e'],
                    'kunci_jawaban' => strtolower($row['kunci_jawaban']),
                    'is_active' => true,
                ]);

                $imported++;
            } catch (\Exception $e) {
                $errors[] = 'Baris ' . ($index + 2) . ': ' . $e->getMessage();
            }
        }

        $message = "Berhasil mengimport {$imported} soal.";
        if (! empty($errors)) {
            $message .= ' Error pada beberapa baris: ' . implode('; ', array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= ' dan ' . (count($errors) - 5) . ' error lainnya.';
            }
        }

        $q = array_filter([
            'ujian_id' => $request->input('ujian_id'),
            'course_id' => $request->input('course_id'),
        ], fn ($v) => $v !== null && $v !== '');

        return redirect()->route('admin.bank-soal', $q)->with('success', $message);
    }

    public function uploadSoalImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
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

    public function getCoursesByGuru($guruId)
    {
        $courses = Course::where('guru_id', $guruId)->get(['id', 'nama_mata_pelajaran', 'kelas']);
        return response()->json(['data' => $courses]);
    }

    public function uploadBankSoalImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('soal_images', $filename, 'public');

            $fileUrl = asset('storage/' . $path);
            
            \Log::info('Image uploaded successfully', [
                'filename' => $filename,
                'path' => $path,
                'url' => $fileUrl,
            ]);

            return response()->json([
                'success' => true,
                'file' => [
                    'name' => $filename,
                    'url' => $fileUrl,
                    'path' => $path
                ]
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Image upload error', [
                'error' => $e->getMessage(),
                'file' => $request->file('file') ? $request->file('file')->getClientOriginalName() : 'unknown',
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah gambar: ' . $e->getMessage()
            ], 400);
        }
    }
}

