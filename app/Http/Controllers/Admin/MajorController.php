<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Major;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Major::query();

        if (request('q')) {
            $q = request('q');
            $query->where(function($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                  ->orWhere('code', 'like', "%$q%")
                  ->orWhere('description', 'like', "%$q%");
            });
        }

        if (request()->filled('status')) {
            $query->where('is_active', request('status') === 'active');
        }

        $majors = $query->latest()->paginate(10)->withQueryString();
        return view('admin.majors.index', compact('majors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.majors.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:majors,code',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        Major::create($validated);
        return redirect()->route('admin.majors.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Major $major)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Major $major)
    {
        return view('admin.majors.edit', compact('major'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:20|unique:majors,code,' . $major->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);
        $validated['is_active'] = $request->boolean('is_active');
        $major->update($validated);
        return redirect()->route('admin.majors.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Major $major)
    {
        $major->delete();
        return back()->with('success', 'Jurusan berhasil dihapus.');
    }

    // Import/Export/Print
    public function exportExcel()
    {
        $data = Major::orderBy('name')->get();
        return Excel::download(new \App\Exports\MajorsExport($data), 'jurusan.xlsx');
    }

    public function exportCsv()
    {
        $data = Major::orderBy('name')->get();
        return Excel::download(new \App\Exports\MajorsExport($data), 'jurusan.csv');
    }

    public function exportPdf()
    {
        $data = Major::orderBy('name')->get();
        $pdf = Pdf::loadView('admin.majors.print', ['majors' => $data]);
        return $pdf->download('jurusan.pdf');
    }

    public function exportWord()
    {
        $data = Major::orderBy('name')->get();
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();
        $section->addText('Daftar Jurusan');
        foreach ($data as $row) {
            $section->addText($row->code . ' - ' . $row->name);
        }
        $temp = tempnam(sys_get_temp_dir(), 'word');
        $file = $temp . '.docx';
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($file);
        return response()->download($file, 'jurusan.docx')->deleteFileAfterSend(true);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,csv',
        ]);
        Excel::import(new \App\Imports\MajorsImport, $request->file('file'));
        return back()->with('success', 'Import jurusan berhasil.');
    }
}
