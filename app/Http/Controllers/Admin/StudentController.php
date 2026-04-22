<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Major;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $query = User::where('role', 'siswa');

        if (request('q')) {
            $q = request('q');
            $query->where(function($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('nis_nip','like',"%$q%");
            });
        }
        if (request('class_id')) {
            $query->where('class_id', request('class_id'));
        }
        if (request('jurusan')) {
            $query->where('jurusan', request('jurusan'));
        }
        if (request()->filled('status')) {
            $query->where('is_active', request('status')==='active');
        }

        $students = $query->latest()->paginate(10)->withQueryString();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $majors = Major::all();
        return view('admin.students.create', compact('classes', 'majors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
'nis_nip' => 'required|string|max:20',
            'jenis_kelamin' => 'required|in:L,P',
            'class_id' => 'nullable|exists:classes,id',
            'jurusan' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $student = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'siswa',
'nis_nip' => $request->nis_nip,
            'jenis_kelamin' => $request->jenis_kelamin,
            'class_id' => $request->class_id,
            'jurusan' => $request->jurusan,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
            'is_active' => true,
        ]);

        // Update student count for the class
        if ($request->class_id) {
            SchoolClass::where('id', $request->class_id)->increment('student_count');
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dibuat.');
    }

    public function edit($id)
    {
        $student = User::findOrFail($id);
        $classes = SchoolClass::all();
        $majors = Major::all();
        return view('admin.students.edit', compact('student', 'classes', 'majors'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
'nis_nip' => 'required|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
            'class_id' => 'nullable|exists:classes,id',
            'jurusan' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $student = User::findOrFail($id);
        $oldClassId = $student->class_id;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
'nis_nip' => $request->nis_nip,
            'jenis_kelamin' => $request->jenis_kelamin,
            'class_id' => $request->class_id,
            'jurusan' => $request->jurusan,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ];

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }

        $student->update($data);

        // Update student count for classes
        if ($oldClassId && $oldClassId != $request->class_id) {
            SchoolClass::where('id', $oldClassId)->decrement('student_count');
        }
        if ($request->class_id && $oldClassId != $request->class_id) {
            SchoolClass::where('id', $request->class_id)->increment('student_count');
        }

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil diperbarui.');
    }

    public function activate($id)
    {
        $student = User::findOrFail($id);
        $student->update(['is_active' => true]);

        return back()->with('success', 'Siswa berhasil diaktivasi.');
    }

    public function deactivate($id)
    {
        $student = User::findOrFail($id);
        $student->update(['is_active' => false]);

        return back()->with('success', 'Siswa berhasil dinonaktifkan.');
    }

    public function exportExcel()
    {
        $data = $this->filteredStudentsCollection();
        return Excel::download(new \App\Exports\UsersExport($data), 'siswa.xlsx');
    }

    public function exportCsv()
    {
        $data = $this->filteredStudentsCollection();
        return Excel::download(new \App\Exports\UsersExport($data), 'siswa.csv');
    }

    public function exportPdf()
    {
        $data = $this->filteredStudentsCollection();
        $pdf = Pdf::loadView('admin.students.print', ['students' => $data]);
        return $pdf->download('siswa.pdf');
    }

    public function exportWord()
    {
        $data = $this->filteredStudentsCollection();
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Daftar Siswa');
        foreach ($data as $s) {
            $section->addText(($s->name).' | '.($s->email).' | '.($s->nis_nip).' | '.($s->class_id).' - '.($s->jurusan).' | '.($s->is_active?'Aktif':'Nonaktif'));
        }
        $tmp = tempnam(sys_get_temp_dir(), 'word');
        $file = $tmp.'.docx';
        \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007')->save($file);
        return response()->download($file, 'siswa.docx')->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:xlsx,csv']);
        Excel::import(new \App\Imports\UsersImport, $request->file('file'));
        return back()->with('success', 'Import siswa berhasil.');
    }

    public function destroy($id)
    {
        $student = User::findOrFail($id);

        // Decrement student count for the class
        if ($student->class_id) {
            SchoolClass::where('id', $student->class_id)->decrement('student_count');
        }

        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    private function filteredStudentsCollection()
    {
        $query = User::where('role', 'siswa');
        if (request('q')) {
            $q = request('q');
            $query->where(function($w) use ($q) {
                $w->where('name','like',"%$q%")
                  ->orWhere('email','like',"%$q%")
                  ->orWhere('nis_nip','like',"%$q%");
            });
        }
        if (request('class_id')) $query->where('class_id', request('class_id'));
        if (request('jurusan')) $query->where('jurusan', request('jurusan'));
        if (request()->filled('status')) $query->where('is_active', request('status')==='active');
        return $query->orderBy('name')->get();
    }
}
