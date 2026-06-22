<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Soal;
use App\Models\Ujian;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

/**
 * BankSoalAdminController
 *
 * Menangani dua tanggung jawab Admin terkait Bank Soal:
 *
 *  1. BANK SOAL — READ ONLY
 *     Admin dapat melihat seluruh bank soal dari semua guru,
 *     termasuk detail soal di dalamnya.
 *     Admin TIDAK bisa menambah, mengedit, atau menghapus soal/bank soal.
 *
 *  2. UJIAN — CRUD FULL
 *     Admin membuat/mengelola ujian dan memilih bank_soal_id dari
 *     dropdown yang berisi seluruh bank soal (milik semua guru).
 *     Ini adalah implementasi Single Source of Truth:
 *     soal tidak diduplikasi, Ujian hanya menyimpan referensi bank_soal_id.
 *
 * RBAC:
 *  - Middleware 'role:admin' dipasang di constructor.
 *
 * Naming Convention Rute (contoh di routes/web.php):
 *  admin.bank-soal.index
 *  admin.bank-soal.show
 *  admin.ujian.index
 *  admin.ujian.create
 *  admin.ujian.store
 *  admin.ujian.show
 *  admin.ujian.edit
 *  admin.ujian.update
 *  admin.ujian.destroy
 */
class BankSoalAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    // ================================================================
    // BANK SOAL — READ ONLY
    // ================================================================

    /**
     * Daftar semua bank soal dari seluruh guru.
     * Admin bisa memfilter berdasarkan guru atau mata pelajaran.
     *
     * Variabel yang dikirim ke View:
     *  - $bankSoals : Collection semua bank soal (paginated, dengan relasi guru)
     *  - $guruList  : Collection user role='guru' untuk filter dropdown
     */
    public function index(Request $request): View
    {
        $query = BankSoal::with('guru')
            ->withCount('soals');

        // Filter opsional: berdasarkan guru
        if ($request->filled('guru_id')) {
            $query->where('guru_id', $request->guru_id);
        }

        // Filter opsional: berdasarkan mata pelajaran (pencarian teks)
        if ($request->filled('q')) {
            $query->where('nama_mata_pelajaran', 'like', '%' . $request->q . '%');
        }

        $bankSoals = $query->latest()->paginate(15)->withQueryString();

        // Data guru untuk dropdown filter di View
        $guruList = \App\Models\User::where('role', 'guru')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.bank-soal.index', compact('bankSoals', 'guruList'));
    }

    /**
     * Detail bank soal: lihat seluruh soal di dalamnya.
     * Admin hanya boleh READ, tidak ada tombol edit/hapus.
     *
     * Variabel yang dikirim ke View:
     *  - $bankSoal : instance BankSoal beserta relasi guru
     *  - $soals    : Collection Soal dalam bank ini (paginated)
     */
    public function show(BankSoal $bankSoal): View
    {
        $bankSoal->load('guru');

        $soals = $bankSoal->soals()->latest()->paginate(20);

        return view('admin.bank-soal.show', compact('bankSoal', 'soals'));
    }

    // ================================================================
    // UJIAN — CRUD FULL (dengan pemilihan bank_soal_id)
    // ================================================================

    /**
     * Daftar semua ujian.
     *
     * Variabel yang dikirim ke View:
     *  - $ujians : Collection ujian dengan relasi bankSoal (paginated)
     */
    public function ujianIndex(): View
    {
        $ujians = Ujian::with(['bankSoal.guru'])
            ->latest('tanggal_ujian')
            ->paginate(15);

        // Hitung jumlah soal per ujian setelah data diambil
        // Menggunakan 1 query agregat untuk menghindari N+1 problem
        $bankSoalIds = $ujians->pluck('bank_soal_id')->filter()->unique()->values()->toArray();

        // Ambil jumlah soal per bank_soal_id sekaligus (1 query saja)
        $soalCounts = \App\Models\Soal::selectRaw('bank_soal_id, COUNT(*) as jumlah')
            ->whereIn('bank_soal_id', $bankSoalIds)
            ->groupBy('bank_soal_id')
            ->pluck('jumlah', 'bank_soal_id');

        // Inject atribut jumlah_soal ke setiap ujian
        // Di View panggil: $ujian->jumlah_soal
        $ujians->each(function ($ujian) use ($soalCounts) {
            $ujian->jumlah_soal = (int) $soalCounts->get($ujian->bank_soal_id, 0);
        });

        return view('admin.ujian.index', compact('ujians'));
    }

    /**
     * Form buat ujian baru.
     *
     * Variabel yang dikirim ke View:
     *  - $bankSoals : Collection semua bank soal (untuk dropdown pemilihan)
     *                 Format: [{id, nama_mata_pelajaran, kelas, guru.name}]
     *  - $classes   : Collection semua kelas (untuk multi-select)
     *  - $defaultTanggalUjian : string datetime default (7 hari dari sekarang)
     */
    public function ujianCreate(): View
    {
        // Tarik semua bank soal dari tabel bank_soal (Single Source of Truth)
        // Sertakan nama guru agar Admin bisa membedakan bank soal yang namanya sama
        $bankSoals = BankSoal::with('guru')
            ->withCount('soals')
            ->orderBy('nama_mata_pelajaran')
            ->get();

        $classes = SchoolClass::orderBy('name')->get();

        $defaultTanggalUjian = now()->addDays(7)->format('Y-m-d\TH:i');

        return view('admin.ujian.create', compact('bankSoals', 'classes', 'defaultTanggalUjian'));
    }

    /**
     * Simpan ujian baru ke database.
     *
     * Kunci RBAC:
     *  - Admin HANYA menyimpan referensi bank_soal_id.
     *  - Soal tidak diduplikasi ke tabel lain (Single Source of Truth).
     *  - Validasi memastikan bank_soal_id benar-benar ada.
     */
    public function ujianStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'judul_ujian'   => ['required', 'string', 'max:255'],
            'bank_soal_id'  => ['required', 'exists:bank_soal,id'],
            'class_ids'     => ['required', 'array', 'min:1'],
            'class_ids.*'   => ['exists:classes,id'],
            'waktu_mulai'   => ['required', 'date', 'after:now'],
            'durasi_menit'  => ['required', 'integer', 'min:1'],
            'bobot_nilai'   => ['required', 'integer', 'min:1', 'max:100'],
            'soal_acak'     => ['required', 'boolean'],
            'jawaban_acak'  => ['required', 'boolean'],
            'tampilkan_hasil' => ['required', 'boolean'],
            'deskripsi'     => ['nullable', 'string'],
        ]);

        // Parse waktu dari form (timezone Jakarta) ke UTC untuk disimpan
        $waktuMulai = Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $validated['waktu_mulai'],
            'Asia/Jakarta'
        )->setTimezone('UTC');

        Ujian::create([
            'judul'           => $validated['judul_ujian'],
            'bank_soal_id'    => $validated['bank_soal_id'],
            'guru_id'         => null,   // ujian dibuat oleh Admin
            'class_ids'       => $validated['class_ids'],
            'tanggal_ujian'   => $waktuMulai,
            'durasi_menit'    => $validated['durasi_menit'],
            'bobot_nilai'     => $validated['bobot_nilai'],
            'soal_acak'       => $validated['soal_acak'],
            'jawaban_acak'    => $validated['jawaban_acak'],
            'tampilkan_hasil' => $validated['tampilkan_hasil'],
            'deskripsi'       => $validated['deskripsi'] ?? null,
            'is_active'       => true,
        ]);

        return redirect()
            ->route('admin.ujian.index')
            ->with('success', 'Ujian berhasil dibuat.');
    }

    /**
     * Detail ujian.
     *
     * Variabel yang dikirim ke View:
     *  - $ujian    : instance Ujian dengan relasi bankSoal dan guru
     *  - $soals    : soal-soal dari bank soal yang dipilih (READ untuk preview)
     *  - $classes  : kelas yang terdaftar di ujian ini
     */
    public function ujianShow(Ujian $ujian): View
    {
        $ujian->load(['bankSoal.guru', 'bankSoal.soals']);

        // Kelas-kelas yang terdaftar pada ujian ini
        $classIds = is_array($ujian->class_ids)
            ? $ujian->class_ids
            : json_decode($ujian->class_ids ?? '[]', true) ?? [];

        $classes = SchoolClass::whereIn('id', $classIds)->get();

        // Soal dari bank soal yang dipilih (hanya untuk preview, READ-only)
        $soals = $ujian->bankSoal ? $ujian->bankSoal->soals()->paginate(20) : collect();

        return view('admin.ujian.show', compact('ujian', 'classes', 'soals'));
    }

    /**
     * Form edit ujian.
     *
     * Variabel yang dikirim ke View:
     *  - $ujian      : instance Ujian yang akan diedit
     *  - $bankSoals  : semua bank soal (untuk dropdown penggantian referensi)
     *  - $classes    : semua kelas
     */
    public function ujianEdit(Ujian $ujian): View
    {
        $bankSoals = BankSoal::with('guru')
            ->withCount('soals')
            ->orderBy('nama_mata_pelajaran')
            ->get();

        $classes = SchoolClass::orderBy('name')->get();

        return view('admin.ujian.edit', compact('ujian', 'bankSoals', 'classes'));
    }

    /**
     * Update ujian.
     */
    public function ujianUpdate(Request $request, Ujian $ujian): RedirectResponse
    {
        $validated = $request->validate([
            'judul_ujian'     => ['required', 'string', 'max:255'],
            'bank_soal_id'    => ['required', 'exists:bank_soal,id'],
            'class_ids'       => ['required', 'array', 'min:1'],
            'class_ids.*'     => ['exists:classes,id'],
            'waktu_mulai'     => ['required', 'date'],
            'durasi_menit'    => ['required', 'integer', 'min:1'],
            'bobot_nilai'     => ['required', 'integer', 'min:1', 'max:100'],
            'soal_acak'       => ['required', 'boolean'],
            'jawaban_acak'    => ['required', 'boolean'],
            'tampilkan_hasil' => ['required', 'boolean'],
            'deskripsi'       => ['nullable', 'string'],
            'is_active'       => ['boolean'],
        ]);

        $waktuMulai = Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $validated['waktu_mulai'],
            'Asia/Jakarta'
        )->setTimezone('UTC');

        $ujian->update([
            'judul'           => $validated['judul_ujian'],
            'bank_soal_id'    => $validated['bank_soal_id'],
            'class_ids'       => $validated['class_ids'],
            'tanggal_ujian'   => $waktuMulai,
            'durasi_menit'    => $validated['durasi_menit'],
            'bobot_nilai'     => $validated['bobot_nilai'],
            'soal_acak'       => $validated['soal_acak'],
            'jawaban_acak'    => $validated['jawaban_acak'],
            'tampilkan_hasil' => $validated['tampilkan_hasil'],
            'deskripsi'       => $validated['deskripsi'] ?? null,
            'is_active'       => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.ujian.show', $ujian)
            ->with('success', 'Ujian berhasil diperbarui.');
    }

    /**
     * Hapus ujian.
     * Bank soal dan soal di dalamnya TIDAK ikut terhapus
     * (sesuai spesifikasi: soal adalah aset guru, bukan milik ujian).
     */
    public function ujianDestroy(Ujian $ujian): RedirectResponse
    {
        $ujian->delete();

        return redirect()
            ->route('admin.ujian.index')
            ->with('success', 'Ujian berhasil dihapus.');
    }
}
