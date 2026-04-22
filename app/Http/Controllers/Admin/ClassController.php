<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Major;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ClassController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$majors = Major::orderBy('name')->get();
		$query = SchoolClass::with('major')->withCount('students');

		if (request('q')) {
			$q = request('q');
			$query->where(function($w) use ($q) {
				$w->where('name', 'like', "%$q%")
				  ->orWhere('homeroom_teacher', 'like', "%$q%");
			});
		}
		if (request('major_id')) {
			$query->where('major_id', request('major_id'));
		}
		if (request()->filled('status')) {
			$query->where('is_active', request('status') === 'active');
		}

		$classes = $query->latest()->paginate(10)->withQueryString();
		return view('admin.classes.index', compact('classes','majors'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$majors = Major::orderBy('name')->get();
		return view('admin.classes.create', compact('majors'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$validated = $request->validate([
			'name' => 'required|string|max:20',
			'major_id' => 'required|exists:majors,id',
			'year' => 'nullable|integer|min:2000|max:2100',
			'homeroom_teacher' => 'nullable|string|max:100',
			'is_active' => 'nullable|boolean',
		]);
		$validated['is_active'] = $request->boolean('is_active');
		SchoolClass::create($validated);
		return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil ditambahkan.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show(SchoolClass $class)
	{
		$students = $class->students()->with('schoolClass.major')->paginate(10);
		return view('admin.classes.show', compact('class', 'students'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(SchoolClass $class)
	{
		$majors = Major::orderBy('name')->get();
		return view('admin.classes.edit', ['class' => $class, 'majors' => $majors]);
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, SchoolClass $schoolClass)
	{
		// Handle empty year field
		if ($request->year === '') {
			$request->merge(['year' => null]);
		}

		$validated = $request->validate([
			'name' => 'required|string|max:20',
			'major_id' => 'required|exists:majors,id',
			'year' => 'nullable|integer|min:2000|max:2100',
			'homeroom_teacher' => 'nullable|string|max:100',
			'is_active' => 'nullable|boolean',
		]);
		$validated['is_active'] = $request->boolean('is_active');
		$schoolClass->update($validated);
		return redirect()->route('admin.classes.index')->with('success', 'Kelas berhasil diperbarui.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(SchoolClass $class)
	{
		$class->delete();
		return back()->with('success', 'Kelas berhasil dihapus.');
	}

	// Import/Export/Print
	public function exportExcel()
	{
		$data = SchoolClass::with('major')->orderBy('name')->get();
		return Excel::download(new \App\Exports\ClassesExport($data), 'kelas.xlsx');
	}
	public function exportCsv()
	{
		$data = SchoolClass::with('major')->orderBy('name')->get();
		return Excel::download(new \App\Exports\ClassesExport($data), 'kelas.csv');
	}
	public function exportPdf()
	{
		$data = SchoolClass::with('major')->orderBy('name')->get();
		$pdf = Pdf::loadView('admin.classes.print', ['classes' => $data]);
		return $pdf->download('kelas.pdf');
	}
	public function exportWord()
	{
		$data = SchoolClass::with('major')->orderBy('name')->get();
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$section = $phpWord->addSection();
		$section->addText('Daftar Kelas');
		foreach ($data as $row) {
			$section->addText(($row->name) . ' - ' . ($row->major->name ?? ''));
		}
		$tmp = tempnam(sys_get_temp_dir(), 'word');
		$file = $tmp . '.docx';
		\PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007')->save($file);
		return response()->download($file, 'kelas.docx')->deleteFileAfterSend(true);
	}
	public function import(Request $request)
	{
		$request->validate(['file' => 'required|file|mimes:xlsx,csv']);
		Excel::import(new \App\Imports\ClassesImport, $request->file('file'));
		return back()->with('success', 'Import kelas berhasil.');
	}
}
