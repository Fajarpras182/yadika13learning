<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class TeacherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $query = User::where('role', 'guru');

        if (request('q')) {
            $q = request('q');
            $query->where(function($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('nis_nip','like',"%$q%");
            });
        }
        if (request()->filled('status')) {
            $query->where('is_active', request('status')==='active');
        }

        $teachers = $query->latest()->paginate(10)->withQueryString();
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'nis_nip' => 'required|string|max:20',
'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:P,L',
            'agama' => 'nullable|string|max:50',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'guru',
            'nis_nip' => $request->nis_nip,
            'no_hp' => $request->no_hp,
'alamat' => $request->alamat,
            'jenis_kelamin' => $request->jenis_kelamin,
            'agama' => $request->agama,
            'is_active' => true,
        ];

        User::create($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dibuat.');
    }

    public function edit($id)
    {
        $teacher = User::findOrFail($id);
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
'nis_nip' => 'required|string|max:20',
            'jenis_kelamin' => 'nullable|in:P,L',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
            'agama' => 'nullable|string|max:50',
        ]);

        $teacher = User::findOrFail($id);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
'nis_nip' => $request->nis_nip,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'agama' => $request->agama,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $photoPath;
        }

        $teacher->update($data);

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $teacher = User::findOrFail($id);
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Guru berhasil dihapus.');
    }

    public function activate($id)
    {
        $teacher = User::findOrFail($id);
        $teacher->update(['is_active' => true]);

        return back()->with('success', 'Guru berhasil diaktivasi.');
    }

    public function deactivate($id)
    {
        $teacher = User::findOrFail($id);
        $teacher->update(['is_active' => false]);

        return back()->with('success', 'Guru berhasil dinonaktifkan.');
    }

    public function exportExcel()
    {
        $data = $this->filteredTeachersCollection();
        return Excel::download(new \App\Exports\UsersExport($data), 'guru.xlsx');
    }

    public function exportCsv()
    {
        $data = $this->filteredTeachersCollection();
        return Excel::download(new \App\Exports\UsersExport($data), 'guru.csv');
    }

    public function exportPdf()
    {
        $data = $this->filteredTeachersCollection();
        $pdf = Pdf::loadView('admin.teachers.print', ['teachers' => $data]);
        return $pdf->download('guru.pdf');
    }

    public function exportWord()
    {
        $data = $this->filteredTeachersCollection();
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Daftar Guru');
        foreach ($data as $t) {
            $section->addText(($t->name).' | '.($t->email).' | '.($t->nis_nip).' | '.($t->is_active?'Aktif':'Nonaktif'));
        }
        $tmp = tempnam(sys_get_temp_dir(), 'word');
        $file = $tmp.'.docx';
        \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007')->save($file);
        return response()->download($file, 'guru.docx')->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);
        Excel::import(new \App\Imports\UsersImport, $request->file('file'));
        return back()->with('success', 'Import guru berhasil.');
    }

    private function filteredTeachersCollection()
    {
        $query = User::where('role', 'guru');
        if (request('q')) {
            $q = request('q');
            $query->where(function($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('nis_nip','like',"%$q%");
            });
        }
        if (request()->filled('status')) $query->where('is_active', request('status')==='active');
        return $query->orderBy('name')->get();
    }
}
