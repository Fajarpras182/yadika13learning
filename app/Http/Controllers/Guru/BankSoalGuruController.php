<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\Soal;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * BankSoalGuruController
 *
 * Menangani CRUD Bank Soal dan Soal untuk role Guru.
 *
 * RBAC:
 *  - Semua method secara eksplisit memfilter data berdasarkan
 *    guru_id = auth()->id() → Guru TIDAK bisa mengakses data guru lain.
 *  - Middleware 'role:guru' dipasang di constructor sebagai gate pertama.
 *
 * Naming Convention Rute (contoh di routes/web.php):
 *  guru.bank-soal.index
 *  guru.bank-soal.create
 *  guru.bank-soal.store
 *  guru.bank-soal.edit
 *  guru.bank-soal.update
 *  guru.bank-soal.destroy
 *  guru.soal.store
 *  guru.soal.edit
 *  guru.soal.update
 *  guru.soal.destroy
 */
class BankSoalGuruController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:guru']);
    }

    // ================================================================
    // BANK SOAL — CRUD
    // ================================================================

    /**
     * Daftar bank soal MILIK guru yang sedang login.
     * Guru tidak akan melihat bank soal guru lain.
     *
     * Variabel yang dikirim ke View:
     *  - $bankSoals : Collection bank soal milik guru (paginated)
     */
    public function index(): View
    {
        $bankSoals = BankSoal::where('guru_id', Auth::id())
            ->withCount('soals')          // tambahkan atribut soals_count
            ->latest()
            ->paginate(15);

        return view('guru.bank-soal.index', compact('bankSoals'));
    }

    /**
     * Form tambah bank soal baru.
     *
     * Variabel yang dikirim ke View:
     *  - (tidak ada; form sederhana hanya butuh input nama_mata_pelajaran & kelas)
     */
    public function create(): View
    {
        return view('guru.bank-soal.create');
    }

    /**
     * Simpan bank soal baru ke database.
     * guru_id di-inject dari Auth, tidak dipercaya dari input form.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelas'               => ['required', 'string', 'max:20'],
            'deskripsi'           => ['nullable', 'string'],
        ]);

        // Inject guru_id dari sesi yang sedang login (bukan dari request)
        $validated['guru_id'] = Auth::id();

        BankSoal::create($validated);

        return redirect()
            ->route('guru.bank-soal.index')
            ->with('success', 'Bank soal berhasil dibuat.');
    }

    /**
     * Detail bank soal beserta daftar soal di dalamnya.
     *
     * Variabel yang dikirim ke View:
     *  - $bankSoal : instance BankSoal (milik guru ini)
     *  - $soals    : Collection Soal dalam bank ini (paginated)
     */
    public function show(BankSoal $bankSoal): View
    {
        // RBAC: pastikan bank soal ini milik guru yang login
        $this->authorizeOwnership($bankSoal);

        $soals = $bankSoal->soals()->latest()->paginate(20);

        return view('guru.bank-soal.show', compact('bankSoal', 'soals'));
    }

    /**
     * Form edit bank soal.
     *
     * Variabel yang dikirim ke View:
     *  - $bankSoal : instance BankSoal yang akan diedit
     */
    public function edit(BankSoal $bankSoal): View
    {
        $this->authorizeOwnership($bankSoal);

        return view('guru.bank-soal.edit', compact('bankSoal'));
    }

    /**
     * Update data bank soal.
     */
    public function update(Request $request, BankSoal $bankSoal): RedirectResponse
    {
        $this->authorizeOwnership($bankSoal);

        $validated = $request->validate([
            'nama_mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelas'               => ['required', 'string', 'max:20'],
            'deskripsi'           => ['nullable', 'string'],
        ]);

        $bankSoal->update($validated);

        return redirect()
            ->route('guru.bank-soal.show', $bankSoal)
            ->with('success', 'Bank soal berhasil diperbarui.');
    }

    /**
     * Hapus bank soal beserta seluruh soal di dalamnya (cascade).
     */
    public function destroy(BankSoal $bankSoal): RedirectResponse
    {
        $this->authorizeOwnership($bankSoal);

        $bankSoal->delete(); // cascade ke tabel soal via FK onDelete('cascade')

        return redirect()
            ->route('guru.bank-soal.index')
            ->with('success', 'Bank soal berhasil dihapus.');
    }

    // ================================================================
    // SOAL — CRUD (butir soal di dalam bank soal tertentu)
    // ================================================================

    /**
     * Form tambah soal baru ke dalam bank soal.
     *
     * Variabel yang dikirim ke View:
     *  - $bankSoal : induk bank soal tempat soal akan ditambahkan
     */
    public function createSoal(BankSoal $bankSoal): View
    {
        $this->authorizeOwnership($bankSoal);

        return view('guru.bank-soal.soal.create', compact('bankSoal'));
    }

    /**
     * Simpan soal baru ke dalam bank soal.
     * bank_soal_id diambil dari route model binding, bukan dari form.
     */
    public function storeSoal(Request $request, BankSoal $bankSoal): RedirectResponse
    {
        $this->authorizeOwnership($bankSoal);

        $validated = $request->validate([
            'teks_pertanyaan' => ['required', 'string'],
            'opsi_a'          => ['required', 'string'],
            'opsi_b'          => ['required', 'string'],
            'opsi_c'          => ['required', 'string'],
            'opsi_d'          => ['required', 'string'],
            'jawaban_benar'   => ['required', 'in:a,b,c,d'],
        ]);

        // Pastikan soal terikat ke bank soal yang benar (bukan dari form input)
        $validated['bank_soal_id'] = $bankSoal->id;

        Soal::create($validated);

        return redirect()
            ->route('guru.bank-soal.show', $bankSoal)
            ->with('success', 'Soal berhasil ditambahkan.');
    }

    /**
     * Form edit soal.
     *
     * Variabel yang dikirim ke View:
     *  - $bankSoal : bank soal induk
     *  - $soal     : soal yang akan diedit
     */
    public function editSoal(BankSoal $bankSoal, Soal $soal): View
    {
        $this->authorizeOwnership($bankSoal);
        $this->authorizeSoalOwnership($bankSoal, $soal);

        return view('guru.bank-soal.soal.edit', compact('bankSoal', 'soal'));
    }

    /**
     * Update soal.
     */
    public function updateSoal(Request $request, BankSoal $bankSoal, Soal $soal): RedirectResponse
    {
        $this->authorizeOwnership($bankSoal);
        $this->authorizeSoalOwnership($bankSoal, $soal);

        $validated = $request->validate([
            'teks_pertanyaan' => ['required', 'string'],
            'opsi_a'          => ['required', 'string'],
            'opsi_b'          => ['required', 'string'],
            'opsi_c'          => ['required', 'string'],
            'opsi_d'          => ['required', 'string'],
            'jawaban_benar'   => ['required', 'in:a,b,c,d'],
        ]);

        $soal->update($validated);

        return redirect()
            ->route('guru.bank-soal.show', $bankSoal)
            ->with('success', 'Soal berhasil diperbarui.');
    }

    /**
     * Hapus soal dari bank soal.
     */
    public function destroySoal(BankSoal $bankSoal, Soal $soal): RedirectResponse
    {
        $this->authorizeOwnership($bankSoal);
        $this->authorizeSoalOwnership($bankSoal, $soal);

        $soal->delete();

        return redirect()
            ->route('guru.bank-soal.show', $bankSoal)
            ->with('success', 'Soal berhasil dihapus.');
    }

    // ================================================================
    // PRIVATE HELPERS — RBAC Guards
    // ================================================================

    /**
     * Pastikan bank soal ini dimiliki oleh guru yang sedang login.
     * Jika bukan miliknya, lempar 403 Forbidden.
     */
    private function authorizeOwnership(BankSoal $bankSoal): void
    {
        if ($bankSoal->guru_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke bank soal ini.');
        }
    }

    /**
     * Pastikan soal memang berada di dalam bank soal yang ditentukan.
     * Mencegah manipulasi parameter ID di URL.
     */
    private function authorizeSoalOwnership(BankSoal $bankSoal, Soal $soal): void
    {
        if ($soal->bank_soal_id !== $bankSoal->id) {
            abort(403, 'Soal ini tidak berada dalam bank soal yang ditentukan.');
        }
    }
}
