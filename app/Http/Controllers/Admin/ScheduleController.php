<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Course;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ScheduleController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index()
	{
		$courses = Course::orderBy('nama_mata_pelajaran')->get();
		$classes = SchoolClass::orderBy('name')->get();
		$query = Schedule::with(['course','schoolClass']);

		if (request('course_id')) $query->where('course_id', request('course_id'));
		if (request('class_id')) $query->where('class_id', request('class_id'));
		if (request('day')) $query->where('day', request('day'));
		if (request()->filled('status')) $query->where('is_active', request('status')==='active');

		$schedules = $query->latest()->paginate(10)->withQueryString();
		return view('admin.schedules.index', compact('schedules','courses','classes'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create()
	{
		$courses = Course::orderBy('nama_mata_pelajaran')->get();
		$classes = SchoolClass::orderBy('name')->get();
		return view('admin.schedules.create', compact('courses','classes'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request)
	{
		$request->validate([
			'course_id' => 'required|exists:courses,id',
			'class_id' => 'required|exists:classes,id',
			'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
			'start_time' => 'required|date_format:Y-m-d\TH:i',
			'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',
			'room' => 'nullable|string|max:50',
			'is_active' => 'nullable|boolean',
		]);

		// Extract time part from datetime-local input
		$startTime = date('H:i', strtotime($request->start_time));
		$endTime = date('H:i', strtotime($request->end_time));

		Schedule::create([
			'course_id' => $request->course_id,
			'class_id' => $request->class_id,
			'day' => $request->day,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'room' => $request->room,
			'is_active' => $request->boolean('is_active'),
		]);

		return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit(Schedule $schedule)
	{
		$courses = Course::orderBy('nama_mata_pelajaran')->get();
		$classes = SchoolClass::orderBy('name')->get();
		return view('admin.schedules.edit', compact('schedule','courses','classes'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Schedule $schedule)
	{
		$request->validate([
			'course_id' => 'required|exists:courses,id',
			'class_id' => 'required|exists:classes,id',
			'day' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
			'start_time' => 'required|date_format:Y-m-d\TH:i',
			'end_time' => 'required|date_format:Y-m-d\TH:i|after:start_time',
			'room' => 'nullable|string|max:50',
			'is_active' => 'nullable|boolean',
		]);

		// Extract time part from datetime-local input
		$startTime = date('H:i', strtotime($request->start_time));
		$endTime = date('H:i', strtotime($request->end_time));

		$schedule->update([
			'course_id' => $request->course_id,
			'class_id' => $request->class_id,
			'day' => $request->day,
			'start_time' => $startTime,
			'end_time' => $endTime,
			'room' => $request->room,
			'is_active' => $request->boolean('is_active'),
		]);

		return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
	}

	/**
	 * Remove the specified resource from storage.
	 */
	public function destroy(Schedule $schedule)
	{
		$schedule->delete();
		return back()->with('success', 'Jadwal berhasil dihapus.');
	}

	// Import/Export/Print
	public function exportExcel()
	{
		$data = Schedule::with(['course','schoolClass'])->orderBy('day')->get();
		return Excel::download(new \App\Exports\SchedulesExport($data), 'jadwal.xlsx');
	}
	public function exportCsv()
	{
		$data = Schedule::with(['course','schoolClass'])->orderBy('day')->get();
		return Excel::download(new \App\Exports\SchedulesExport($data), 'jadwal.csv');
	}
	public function exportPdf()
	{
		$data = Schedule::with(['course','schoolClass'])->orderBy('day')->get();
		$pdf = Pdf::loadView('admin.schedules.print', ['schedules' => $data]);
		return $pdf->download('jadwal.pdf');
	}
	public function exportWord()
	{
		$data = Schedule::with(['course','schoolClass'])->orderBy('day')->get();
		$phpWord = new \PhpOffice\PhpWord\PhpWord();
		$section = $phpWord->addSection();
		$section->addText('Daftar Jadwal');
		foreach ($data as $row) {
			$section->addText($row->day.' '.$row->start_time.'-'.$row->end_time.' | '.($row->course->nama_mata_pelajaran ?? '').' | '.($row->schoolClass->name ?? ''));
		}
		$tmp = tempnam(sys_get_temp_dir(), 'word');
		$file = $tmp . '.docx';
		\PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007')->save($file);
		return response()->download($file, 'jadwal.docx')->deleteFileAfterSend(true);
	}
	public function import(Request $request)
	{
		$request->validate(['file' => 'required|file|mimes:xlsx,csv']);
		Excel::import(new \App\Imports\SchedulesImport, $request->file('file'));
		return back()->with('success', 'Import jadwal berhasil.');
	}
}
